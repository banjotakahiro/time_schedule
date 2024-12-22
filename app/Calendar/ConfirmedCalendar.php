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
    protected $month; // 基準月
    protected $dayOffs = []; // ユーザーごとの休み情報

    public function __construct(string $month)
    {
        if (empty($month)) {
            throw new InvalidArgumentException('月が指定されていません。');
        }
        $this->month = $month;
    }

    public function setDayOffs(array $dayOffs): void
    {
        $this->dayOffs = $dayOffs;
    }

    public function generateShiftPlan(): array
    {
        $finalShifts = []; // 最終的なシフト表
        $assignedUsers = []; // 既に割り当てられたユーザー

        $requestedShifts = Requested_shift::where('date', 'like', "{$this->month}%")->get();
        $informationShifts = Information_shift::where('date', 'like', "{$this->month}%")->get();
        $constraints = ShiftConstraint::where('month', $this->month)->get();

        foreach ($informationShifts as $infoShift) {
            $assignedUsersForDay = []; // 日ごとの割り当てを追跡
            $rolesWithCounts = [
                ['role' => $infoShift->role1, 'count' => $infoShift->required_staff_role1],
                ['role' => $infoShift->role2, 'count' => $infoShift->required_staff_role2],
                ['role' => $infoShift->role3, 'count' => $infoShift->required_staff_role3],
            ];

            foreach ($rolesWithCounts as $roleInfo) {
                if (!$roleInfo['role'] || $roleInfo['count'] <= 0) {
                    continue;
                }

                $remainingCount = $roleInfo['count'];

                $this->applyMandatoryShifts($finalShifts, $assignedUsersForDay, $constraints, $infoShift, $roleInfo, $remainingCount);
                $this->applyPairingConstraints($constraints, $infoShift, $roleInfo);
                $this->applyShiftLimitConstraints($constraints, $infoShift, $roleInfo);

                if ($remainingCount > 0) {
                    $this->processRequestsForRole($finalShifts, $assignedUsersForDay, $requestedShifts, $infoShift, $roleInfo, $remainingCount);
                }
            }
        }

        return $finalShifts;
    }

    private function applyMandatoryShifts(array &$finalShifts, array &$assignedUsers, $constraints, $infoShift, $roleInfo, int &$remainingCount): void
    {
        foreach ($constraints as $constraint) {
            if ($constraint->status === 'mandatory_shift' && $constraint->date === $infoShift->date) {
                $finalShifts[] = [
                    'date' => $infoShift->date,
                    'start_time' => $infoShift->start_time,
                    'end_time' => $infoShift->end_time,
                    'user_id' => $constraint->user_id,
                    'role' => $roleInfo['role'],
                    'status' => 'mandatory_shift',
                ];
                $assignedUsers[] = $constraint->user_id;
                $remainingCount--;
            }
        }
    }

    private function applyPairingConstraints($constraints, $infoShift, $roleInfo): void
    {
        foreach ($constraints as $constraint) {
            if ($constraint->status === 'pairing') {
                // ロジック: 必ず一緒にする/しない
            }
        }
    }

    private function applyShiftLimitConstraints($constraints, $infoShift, $roleInfo): void
    {
        foreach ($constraints as $constraint) {
            if ($constraint->status === 'shift_limit') {
                // ロジック: 1週間または月単位でのシフト回数制限
            }
        }
    }

    private function processRequestsForRole(array &$finalShifts, array &$assignedUsers, $requestedShifts, $infoShift, $roleInfo, int &$remainingCount): void
    {
        $requestsForRole = $requestedShifts->filter(function ($reqShift) use ($infoShift, $roleInfo, $assignedUsers) {
            $isDayOff = isset($this->dayOffs[$reqShift->user_id]) &&
                in_array($infoShift->date, $this->dayOffs[$reqShift->user_id]);

            return $infoShift->date === $reqShift->date
                && $infoShift->start_time <= $reqShift->start_time
                && $infoShift->end_time >= $reqShift->end_time
                && in_array($roleInfo['role'], $this->getUserRoles($reqShift->user_id))
                && !in_array($reqShift->user_id, $assignedUsers)
                && !$isDayOff;
        });

        foreach ($requestsForRole as $request) {
            if ($remainingCount <= 0) {
                break;
            }

            $finalShifts[] = [
                'date' => $infoShift->date,
                'start_time' => $infoShift->start_time,
                'end_time' => $infoShift->end_time,
                'user_id' => $request->user_id,
                'role' => $roleInfo['role'],
                'status' => 'processed_from_multiple',
            ];
            $assignedUsers[] = $request->user_id;
            $remainingCount--;
        }

        // 必要人数を満たせなかった場合の処理
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


    private function getUserRoles(int $userId): array
    {
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
