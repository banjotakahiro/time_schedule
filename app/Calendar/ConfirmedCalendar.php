<?php

namespace App\Calendar;

use App\Models\Employee;
use App\Models\Requested_shift;
use App\Models\Information_shift;
use InvalidArgumentException;

class ConfirmedCalendar
{
    protected $month; // 基準月

    /**
     * コンストラクタで基準月を受け取る
     *
     * @param string $month 指定した月 (例: '2024-12')
     * @throws InvalidArgumentException
     */
    public function __construct(string $month)
    {
        if (empty($month)) {
            throw new InvalidArgumentException('月が指定されていません。');
        }
        $this->month = $month;
    }

    /**
     * シフト表を生成する
     *
     * @return array
     */
    public function generateShiftPlan(): array
    {
        $finalShifts = []; // 最終的なシフト表
        $unprocessedShifts = []; // 未処理のシフト

        // 指定された月のリクエストシフトと情報シフトを取得
        $requestedShifts = Requested_shift::where('date', 'like', "{$this->month}%")->get();
        $informationShifts = Information_shift::where('date', 'like', "{$this->month}%")->get();

        // 各日程ごとに情報シフトを処理
        foreach ($informationShifts as $infoShift) {
            // その日のリクエストを取得
            $requestsForDay = $requestedShifts->filter(function ($reqShift) use ($infoShift) {
                return $infoShift->date === $reqShift->date &&
                    $infoShift->start_time >= $reqShift->start_time &&
                    $infoShift->end_time <= $reqShift->end_time;
            });

            if ($requestsForDay->isEmpty()) {
                $finalShifts[] = [
                    'date' => $infoShift->date,
                    'start_time' => $infoShift->start_time,
                    'end_time' => $infoShift->end_time,
                    'user_id' => null,
                    'status' => 'no_applicant',
                ];
            } elseif ($requestsForDay->count() === 1) {
                // 一人だけ申請している場合
                $singleRequest = $requestsForDay->first();
                $isRoleMatchResult = $this->isRoleMatch($infoShift, $singleRequest);

                if ($isRoleMatchResult['is_match']) {
                    $finalShifts[] = [
                        'date' => $infoShift->date,
                        'start_time' => $infoShift->start_time,
                        'end_time' => $infoShift->end_time,
                        'user_id' => $singleRequest->user_id,
                        'status' => 'confirmed_single',
                        'matched_roles' => $isRoleMatchResult['matched_roles'], // 一致した役割を格納
                    ];
                } else {
                    $finalShifts[] = [
                        'date' => $infoShift->date,
                        'start_time' => $infoShift->start_time,
                        'end_time' => $infoShift->end_time,
                        'user_id' => null,
                        'status' => 'role_mismatch',
                        'matched_roles' => [] // 一致なし
                    ];
                }
            } else {
                $unprocessedShifts[] = [
                    'date' => $infoShift->date,
                    'start_time' => $infoShift->start_time,
                    'end_time' => $infoShift->end_time,
                    'status' => 'multiple_applicants',
                    'applicants' => $requestsForDay->pluck('user_id')->toArray(),
                ];
            }
        }

        // 二人以上申請がある日を埋める処理
        $finalShifts = $this->processMultipleApplicants($finalShifts, $unprocessedShifts, $informationShifts);
        return $finalShifts;
    }

    /**
     * 二人以上の申請がある日を埋める処理
     *
     * @param array $finalShifts 確定したシフト
     * @param array $unprocessedShifts 未処理のシフト
     * @param \Illuminate\Support\Collection $informationShifts 情報シフトデータ
     * @return array
     */

    private function processMultipleApplicants(array $finalShifts, array $unprocessedShifts, $informationShifts): array
    {
        // ユーザーごとの既存シフト数を計算
        $userShiftCounts = [];
        foreach ($finalShifts as $shift) {
            if (!empty($shift['user_id'])) {
                $userShiftCounts[$shift['user_id']] = ($userShiftCounts[$shift['user_id']] ?? 0) + 1;
            }
        }

        // 未処理シフトを埋める
        foreach ($unprocessedShifts as $shift) {
            $infoShift = $informationShifts->firstWhere('date', $shift['date']);

            // 条件を満たすユーザーをフィルタリング
            $validApplicants = collect($shift['applicants'])->filter(function ($userId) use ($infoShift) {
                $isRoleMatchResult = $this->isRoleMatch($infoShift, (object)['user_id' => $userId]);
                return $isRoleMatchResult['is_match']; // 一致する場合のみ有効
            });

            if ($validApplicants->isEmpty()) {
                $finalShifts[] = [
                    'date' => $shift['date'],
                    'start_time' => $shift['start_time'],
                    'end_time' => $shift['end_time'],
                    'user_id' => null,
                    'status' => 'no_valid_applicant',
                ];
                continue;
            }

            // 最も少ないシフト数のユーザーを選択
            $selectedUser = $validApplicants->sortBy(function ($userId) use ($userShiftCounts) {
                return $userShiftCounts[$userId] ?? 0;
            })->first();

            $isRoleMatchResult = $this->isRoleMatch($infoShift, (object)['user_id' => $selectedUser]);

            $finalShifts[] = [
                'date' => $shift['date'],
                'start_time' => $shift['start_time'],
                'end_time' => $shift['end_time'],
                'user_id' => $selectedUser,
                'status' => 'processed_from_multiple',
                'matched_roles' => $isRoleMatchResult['matched_roles'], // 一致した役割
            ];

            // ユーザーのシフト数を更新
            $userShiftCounts[$selectedUser] = ($userShiftCounts[$selectedUser] ?? 0) + 1;
        }
        return $finalShifts;
    }

    /**
     * 情報シフトの役割条件を満たすか確認
     *
     * @param Information_shift $infoShift 情報シフトデータ
     * @param object $requestShift リクエストシフトデータ (user_id を含む)
     * @return array
     */
    private function isRoleMatch($infoShift, $requestShift): array
    {
        $requiredRoles = [$infoShift->role1, $infoShift->role2, $infoShift->role3];
        $userRoles = $this->getUserRoles($requestShift->user_id);

        // 役割の一致部分を取得
        $matchedRoles = array_intersect($requiredRoles, $userRoles);

        return [
            'is_match' => !empty($matchedRoles),
            'matched_roles' => $matchedRoles
        ];
    }

    /**
     * ユーザーの役割を取得するメソッド
     *
     * @param int $userId
     * @return array
     */
    private function getUserRoles(int $userId): array
    {
        // employees テーブルからスキルを取得
        $employee = Employee::where('user_id', $userId)->first();

        if (!$employee) {
            return [];
        }

        return array_filter([
            $employee->skill1,
            $employee->skill2,
            $employee->skill3,
        ]);
    }
}
