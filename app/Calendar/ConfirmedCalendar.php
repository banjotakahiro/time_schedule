<?php
namespace App\Calendar;

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
                // 誰も申請していない場合
                $finalShifts[] = [
                    'date' => $infoShift->date,
                    'start_time' => $infoShift->start_time,
                    'end_time' => $infoShift->end_time,
                    'user_id' => null, // 空席
                    'status' => 'no_applicant',
                ];
            } elseif ($requestsForDay->count() === 1) {
                // 一人だけ申請している場合
                $singleRequest = $requestsForDay->first();
                $finalShifts[] = [
                    'date' => $infoShift->date,
                    'start_time' => $infoShift->start_time,
                    'end_time' => $infoShift->end_time,
                    'user_id' => $singleRequest->user_id,
                    'status' => 'confirmed',
                ];
            } else {
                // 二人以上申請がある場合
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
        $finalShifts = $this->processMultipleApplicants($finalShifts, $unprocessedShifts);

        return $finalShifts;
    }

    /**
     * 二人以上の申請がある日を埋める処理
     *
     * @param array $finalShifts 確定したシフト
     * @param array $unprocessedShifts 未処理のシフト
     * @return array
     */
    private function processMultipleApplicants(array $finalShifts, array $unprocessedShifts): array
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
            // 最も少ないシフト数のユーザーを選択
            $selectedUser = collect($shift['applicants'])->sortBy(function ($userId) use ($userShiftCounts) {
                return $userShiftCounts[$userId] ?? 0;
            })->first();

            $finalShifts[] = [
                'date' => $shift['date'],
                'start_time' => $shift['start_time'],
                'end_time' => $shift['end_time'],
                'user_id' => $selectedUser,
                'status' => 'confirmed',
            ];

            // ユーザーのシフト数を更新
            $userShiftCounts[$selectedUser] = ($userShiftCounts[$selectedUser] ?? 0) + 1;
        }

        return $finalShifts;
    }
}