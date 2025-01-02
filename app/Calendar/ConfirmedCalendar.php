<?php

namespace App\Calendar;

use App\Models\Employee;
use App\Models\Requested_shift;
use App\Models\Information_shift;
use App\Models\Role;
use App\Models\ShiftConstraint;
use InvalidArgumentException;

class ConfirmedCalendar
{
    // クラス全体の説明: このクラスは指定された月に基づいて、従業員のシフトスケジュールを生成するためのロジックを提供します。

    protected $month; // 基準月
    protected $dayOffs = []; // ユーザーごとの休み情報

    // コンストラクタ: 基準月を設定します。
    public function __construct(string $month)
    {
        if (empty($month)) {
            // 基準月が指定されていない場合は例外をスロー
            throw new InvalidArgumentException('月が指定されていません。');
        }
        $this->month = $month;
    }

    // ユーザーごとの休み情報を設定するメソッド
    public function setDayOffs(array $dayOffs): void
    {
        // $this->dayOffs = $dayOffs;
    }

    // シフトスケジュールを生成するメインのメソッド
    public function generateShiftPlan(): array
    {
        $finalShifts = []; // 最終的なシフト表
        $assignedUsers = []; // 既に割り当てられたユーザー

        // 指定された月のリクエストされたシフトを取得
        $requestedShifts = Requested_shift::where('date', 'like', "{$this->month}%")->get();

        // 指定された月のシフト情報を取得
        $informationShifts = Information_shift::where('date', 'like', "{$this->month}%")->get();

        // 指定された月のシフト制約を取得
        $constraints = ShiftConstraint::where('start_date', 'like', "{$this->month}%")->get();

        foreach ($informationShifts as $infoShift) {
            $assignedUsersForDay = []; // その日の割り当てを追跡する配列

            // 各役割と必要な人数の情報を取得
            $rolesWithCounts = [
                ['role' => $infoShift->role1, 'count' => $infoShift->required_staff_role1],
                ['role' => $infoShift->role2, 'count' => $infoShift->required_staff_role2],
                ['role' => $infoShift->role3, 'count' => $infoShift->required_staff_role3],
            ];

            foreach ($rolesWithCounts as $roleInfo) {
                if (!$roleInfo['role'] || $roleInfo['count'] <= 0) {
                    // 役割が指定されていないか、必要人数が0以下の場合はスキップ
                    continue;
                }

                $remainingCount = $roleInfo['count']; // 割り当てが必要な人数

                // 必須出勤のシフトを適用
                $this->applyMandatoryShifts($finalShifts, $assignedUsersForDay, $constraints, $infoShift, $roleInfo, $remainingCount);

                // ペアリングの制約を適用
                $this->applyPairingConstraints($constraints, $infoShift, $roleInfo);

                // シフト回数制限を適用
                $this->applyShiftLimitConstraints($constraints, $infoShift, $roleInfo);

                // 必要人数が残っている場合、リクエストを処理
                if ($remainingCount > 0) {
                    $this->processRequestsForRole($finalShifts, $assignedUsersForDay, $requestedShifts, $infoShift, $roleInfo, $remainingCount);
                }
            }
        }

        return $finalShifts; // 最終的なシフト表を返す
    }

    // 必須出勤のシフトを適用するメソッド
    private function applyMandatoryShifts(array &$finalShifts, array &$assignedUsers, $constraints, $infoShift, $roleInfo, int &$remainingCount): void
    {
        // 必須出勤制約を確認して適用
    }

    // ペアリング制約（必ず一緒にする/しない）の適用メソッド
    private function applyPairingConstraints($constraints, $infoShift, $roleInfo): void
    {
        // ペアリング制約のロジックを記述
    }

    // シフト回数制限を適用するメソッド
    private function applyShiftLimitConstraints($constraints, $infoShift, $roleInfo): void
    {
        // シフト回数制限のロジックを記述
    }

    // リクエストされたシフトを処理するメソッド
    private function processRequestsForRole(array &$finalShifts, array &$assignedUsersForDay, $requestedShifts, $infoShift, $roleInfo, int &$remainingCount): void
    {
        // リクエストをフィルタリング
        $requestsForRole = $requestedShifts->filter(function ($reqShift) use ($infoShift, $roleInfo, $assignedUsersForDay) {
            $isDayOff = isset($this->dayOffs[$reqShift->user_id]) &&
                in_array($infoShift->date, $this->dayOffs[$reqShift->user_id]);

            return $infoShift->date === $reqShift->date
                && $infoShift->start_time <= $reqShift->start_time
                && $infoShift->end_time >= $reqShift->end_time
                && in_array($roleInfo['role'], $this->getUserRoles($reqShift->user_id))
                && !array_key_exists($reqShift->user_id, $assignedUsersForDay)
                && !$isDayOff;
        });

        // 優先順位に基づいてリクエストを並び替え
        $sortedRequests = $requestsForRole->sortByDesc(function ($reqShift) {
            return $this->getUserPriority($reqShift->user_id);
        });

        // 割り当て処理
        foreach ($sortedRequests as $request) {
            if ($remainingCount <= 0) {
                // 必要人数が満たされた場合終了
                break;
            }

            // シフト表に追加
            $finalShifts[] = [
                'date' => $infoShift->date,
                'start_time' => $infoShift->start_time,
                'end_time' => $infoShift->end_time,
                'user_id' => $request->user_id,
                'role' => $roleInfo['role'],
                'status' => 'processed_from_multiple',
            ];

            // 割り当て済みのユーザーを追跡
            $assignedUsersForDay[$request->user_id] = $roleInfo['role'];
            $remainingCount--;
        }

        // 必要人数を満たせなかった場合、空席を記録
        if ($remainingCount > 0) {
            for ($i = 0; $i < $remainingCount; $i++) {
                $finalShifts[] = [
                    'date' => $infoShift->date,
                    'start_time' => $infoShift->start_time,
                    'end_time' => $infoShift->end_time,
                    'user_id' => null,
                    'role' => $roleInfo['role'],
                    'status' => 'no_applicant',
                ];
            }
        }
    }

    // ユーザーの役割を取得するメソッド
    private function getUserRoles(int $userId): array
    {
        $employee = Employee::where('user_id', $userId)->first();

        if (!$employee) {
            return []; // ユーザーが見つからない場合は空の配列を返す
        }

        // ユーザーのスキルを取得
        return array_filter([
            $employee->skill1,
            $employee->skill2,
            $employee->skill3,
        ]);
    }

    // ユーザーの優先度を取得するメソッド
    private function getUserPriority(int $userId): int
    {
        // Employeeテーブルからpriorityを取得
        $employee = Employee::where('user_id', $userId)->first();

        // priorityが存在しない場合は最低優先度を返す
        return $employee->priority !== null ? $employee->priority : PHP_INT_MAX;
    }
}
