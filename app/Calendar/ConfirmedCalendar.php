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
        $finalShifts = [];
        $assignedUsers = []; // 各日付ごとに既に割り当てられたユーザーを追跡

        $requestedShifts = Requested_shift::where('date', 'like', "{$this->month}%")->get();
        $informationShifts = Information_shift::where('date', 'like', "{$this->month}%")->get();

        // 指定された月のシフト制約を取得し、priority順に並び替える
        $constraints = ShiftConstraint::where(function ($query) {
            $query->where('start_date', '<=', "{$this->month}-31")
                ->where('end_date', '>=', "{$this->month}-01");
        })->get()->sort(function ($a, $b) {
            if ($a->priority === $b->priority) {
                return 0; // priorityが同じ場合そのままの順番
            }
            if ($a->priority === null) {
                return 1; // nullの場合後ろに配置
            }
            if ($b->priority === null) {
                return -1; // nullの場合後ろに配置
            }
            return $a->priority - $b->priority; // 数字が小さい順
        });

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
                    continue;
                }

                $remainingCount = $roleInfo['count']; // 割り当てが必要な人数
                foreach ($constraints as $constraint) {
                    switch ($constraint->status) {
                        case 'day_off':
                            $this->applyDayOffConstraint($assignedUsersForDay, $constraint);
                            break;
                        case 'mandatory_shift':
                            $this->applyMandatoryShifts($finalShifts, $assignedUsers, $constraint, $infoShift, $roleInfo, $remainingCount);
                            break;
                        case 'pairing':
                            $this->applyPairingConstraints($assignedUsersForDay, $constraint);
                            break;
                        case 'no_pairing':
                            $this->applyNoPairingConstraints($assignedUsersForDay, $constraint);
                            break;
                        case 'shift_limit':
                            $this->applyShiftLimitConstraints($assignedUsersForDay, $constraint);
                            break;
                        default:
                            break;
                    }
                }

                if ($remainingCount > 0) {
                    $this->processRequestsForRole($finalShifts, $assignedUsers, $assignedUsersForDay, $requestedShifts, $infoShift, $roleInfo, $remainingCount);
                }
            }
        }
        return $finalShifts; // 最終的なシフト表を返す
    }

    private function applyDayOffConstraint(array &$assignedUsersForDay, $constraint): void
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $assignedUsersForDay[$dateStr][$constraint->user_id] = 'day_off';
        }
    }

    private function applyMandatoryShifts(array &$finalShifts, array &$assignedUsers, $constraint, $infoShift, $roleInfo, int &$remainingCount): void
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if ($remainingCount > 0 && !isset($assignedUsers[$dateStr][$constraint->user_id])) {
                $finalShifts[] = [
                    'date' => $dateStr,
                    'start_time' => $infoShift->start_time,
                    'end_time' => $infoShift->end_time,
                    'user_id' => $constraint->user_id,
                    'role' => $roleInfo['role'],
                    'status' => 'mandatory_shift',
                ];
                $assignedUsers[$dateStr][$constraint->user_id] = $roleInfo['role'];
                $remainingCount--;
            }
        }
    }

    private function applyPairingConstraints(array &$assignedUsersForDay, $constraint): void
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if ($constraint->paired_user_id) {
                $assignedUsersForDay[$dateStr][$constraint->user_id] = 'paired_with_' . $constraint->paired_user_id;
                $assignedUsersForDay[$dateStr][$constraint->paired_user_id] = 'paired_with_' . $constraint->user_id;
            }
        }
    }

    private function applyNoPairingConstraints(array &$assignedUsersForDay, $constraint): void
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if ($constraint->paired_user_id) {
                $assignedUsersForDay[$dateStr][$constraint->user_id] = 'no_pairing_with_' . $constraint->paired_user_id;
                $assignedUsersForDay[$dateStr][$constraint->paired_user_id] = 'no_pairing_with_' . $constraint->user_id;
            }
        }
    }

    private function applyShiftLimitConstraints(array &$assignedUsersForDay, $constraint): void
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        $assignedCount = 0;
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if (isset($assignedUsersForDay[$dateStr][$constraint->user_id])) {
                $assignedCount++;
            }
        }
        $remainingShifts = $constraint->max_shifts - $assignedCount;
        if ($remainingShifts > 0) {
            $assignedUsersForDay[$constraint->user_id] = [
                'shift_limit' => $constraint->max_shifts,
                'remaining_shifts' => $remainingShifts,
            ];
        }
    }

    private function processRequestsForRole(array &$finalShifts, array &$assignedUsers, array &$assignedUsersForDay, $requestedShifts, $infoShift, $roleInfo, int &$remainingCount): void
    {
        $requestsForRole = $requestedShifts->filter(function ($reqShift) use ($infoShift, $roleInfo, $assignedUsersForDay) {
            $isDayOff = isset($assignedUsersForDay[$infoShift->date][$reqShift->user_id]) &&
                $assignedUsersForDay[$infoShift->date][$reqShift->user_id] === 'day_off';

                // ここより上のさっきの要素を正誤判定でうまく機能してないことがあることを考慮しておく
                // ここのデバックめんどくさかった


            return $infoShift->date === $reqShift->date
                && $infoShift->start_time <= $reqShift->start_time
                && $infoShift->end_time >= $reqShift->end_time
                && in_array($roleInfo['role'], $this->getUserRoles($reqShift->user_id))
                && !$isDayOff;
        });

        $sortedRequests = $requestsForRole->sortByDesc(function ($reqShift) {
            return $this->getUserPriority($reqShift->user_id);
        });

        foreach ($sortedRequests as $request) {
            if ($remainingCount <= 0) {
                break;
            }

            // 割り当て済みユーザーを確認
            $dateStr = $infoShift->date;
            if (isset($assignedUsers[$dateStr][$request->user_id])) {
                continue; // 既にその日に割り当てられている場合スキップ
            }

            $finalShifts[] = [
                'date' => $infoShift->date,
                'start_time' => $infoShift->start_time,
                'end_time' => $infoShift->end_time,
                'user_id' => $request->user_id,
                'role' => $roleInfo['role'],
                'status' => 'processed_from_requests',
            ];

            // 割り当て済みユーザーを記録
            $assignedUsers[$dateStr][$request->user_id] = $roleInfo['role'];
            $remainingCount--;
        }

        if ($remainingCount > 0) {
            for ($i = 0; $i < $remainingCount; $i++) {
                $finalShifts[] = [
                    'date' => $infoShift->date,
                    'start_time' => $infoShift->start_time,
                    'end_time' => $infoShift->end_time,
                    'user_id' => null,
                    'role' => $roleInfo['role'],
                    'status' => 'unfilled',
                ];
            }
        }
    }

    private function createDatePeriod(string $startDate, string $endDate): \DatePeriod
    {
        $start = new \DateTime($startDate);
        $end = (new \DateTime($endDate))->modify('+1 day');
        $interval = new \DateInterval('P1D');
        return new \DatePeriod($start, $interval, $end);
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

    private function getUserPriority(int $userId): int
    {
        $employee = Employee::where('user_id', $userId)->first();

        return $employee->priority !== null ? $employee->priority : PHP_INT_MAX;
    }
}
