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
    public function __construct(string $month)
    {
        if (empty($month)) {
            throw new InvalidArgumentException('月が指定されていません。');
        }
        $this->month = $month;
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
            $constraintShiftForDay = []; // その日の割り当てを追跡する配列

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
                // ここのstatusのコードは毎回毎回精査してることになってるからあんまりきれいじゃない。わざわざ配列で保持する必要性がなくなってしまう
                // 時間があったら直したい

                $remainingCount = $roleInfo['count']; // 割り当てが必要な人数
                foreach ($constraints as $constraint) {
                    switch ($constraint->status) {
                        case 'day_off':
                            $this->applyDayOffConstraint($constraintShiftForDay, $constraint);
                            break;
                        case 'mandatory_shift':
                            $this->applyMandatoryShifts($finalShifts, $assignedUsers, $constraint, $infoShift, $roleInfo, $remainingCount);
                            break;
                        case 'pairing':
                            // 1. ペアリングシフトの割り当てを優先
                            // 現在の日付が pairedDates に含まれる場合のみ実行
                            // applyPairingConstraints を呼び出して日付リストを取得
                            // 現在の日付が pairedDates に含まれる場合のみ実行

                            $pairedDates = $this->applyPairingConstraints($constraintShiftForDay, $constraint);
                            if (in_array($infoShift->date, $pairedDates, true)) {
                                // paired_withの制約を確認しつつペアリングシフトを割り当てる
                                $this->processPairingShifts($finalShifts, $assignedUsers, $requestedShifts, $infoShift, $roleInfo, $remainingCount);
                            }
                            break;
                        case 'no_pairing':
                            $this->applyNoPairingConstraints($constraintShiftForDay, $constraint);
                            break;
                        case 'shift_limit':
                            $this->applyShiftLimitConstraints($constraintShiftForDay, $constraint);
                            break;
                        default:
                            break;
                    }
                }

                // 2. 通常のシフト割り当てを実行
                $this->processRequestsForRole($finalShifts, $assignedUsers, $constraintShiftForDay, $requestedShifts, $infoShift, $roleInfo, $remainingCount);
            }
        }
        return $finalShifts; // 最終的なシフト表を返す
    }

    private function applyDayOffConstraint(array &$constraintShiftForDay, $constraint): void
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $constraintShiftForDay[$dateStr][$constraint->user_id] = 'day_off';
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

    private function applyPairingConstraints(array &$constraintShiftForDay, $constraint): array
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        $dates = []; // 日付を格納する配列

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $dateStr; // 日付をリストに追加

            if ($constraint->paired_user_id) {
                $constraintShiftForDay[$dateStr][$constraint->user_id] = 'paired_with_' . $constraint->paired_user_id;
                $constraintShiftForDay[$dateStr][$constraint->paired_user_id] = 'paired_with_' . $constraint->user_id;
            }
        }

        return $dates; // 日付リストを返す
    }

    private function applyNoPairingConstraints(array &$constraintShiftForDay, $constraint): void
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if ($constraint->paired_user_id) {
                $constraintShiftForDay[$dateStr][$constraint->user_id] = 'no_pairing_with_' . $constraint->paired_user_id;
                $constraintShiftForDay[$dateStr][$constraint->paired_user_id] = 'no_pairing_with_' . $constraint->user_id;
            }
        }
    }

    private function applyShiftLimitConstraints(array &$constraintShiftForDay, $constraint): void
    {
        $period = $this->createDatePeriod($constraint->start_date, $constraint->end_date);
        $dates = []; // 日付リストを初期化

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $constraintShiftForDay[$dateStr][$constraint->user_id] = 'max_shifts';
            $dates[] = $dateStr; // 日付をリストに追加
        }

        // max_shifts_explain に日付リストを含める
        $constraintShiftForDay[$constraint->user_id]['max_shifts_explain'] = [
            'shift_limit' => $constraint->max_shifts,
            'current_shifts_count' => 0,
            'dates' => $dates, // 日付リストを保持
        ];
    }


    private function processRequestsForRole(array &$finalShifts, array &$assignedUsers, array &$constraintShiftForDay, $requestedShifts, $infoShift, $roleInfo, int &$remainingCount): void
    {
        $singleRequestShifts = $this->getSingleRequestShifts($requestedShifts, $infoShift, $roleInfo);
        $multipleRequestShifts = $this->getMultipleRequestShifts($requestedShifts, $infoShift, $roleInfo);

        $shiftCounts = []; // 各ユーザーのシフト数を記録

        // ここの処理がなにかやりすぎています。二人以上の申請があっても通り用になっている。
        // 1人だけ申請したシフトを処理

        // これはpriorityも使えないかなりのやらかしですが、先いきます。なぜか、シングルでどんなシフト処理も勝手にやってしまう。その代わり登録する順番がいじれない
        // この二人以上処理が少ない人順に割り当てれるようになれば、no_pairingも解消します！！
        foreach ($singleRequestShifts as $request) {
            if ($this->isUserDayOff($constraintShiftForDay, $infoShift->date, $request->user_id)) {
                continue; // day_offの場合はスキップ
            }

            // ユーザーがすでにその日に別の役割を持っている場合スキップ
            if (isset($assignedUsers[$infoShift->date][$request->user_id])) {
                continue;
            }

            // paired_withの制約を確認
            if ($this->isUserRestrictedByPairing($constraintShiftForDay, $infoShift->date, $request->user_id)) {
                continue; // pairing制約に該当する場合はスキップ
            }

            $this->assignShift($finalShifts, $assignedUsers, $constraintShiftForDay, $request, $infoShift, $roleInfo);
            $remainingCount--;
            $shiftCounts[$request->user_id] = ($shiftCounts[$request->user_id] ?? 0) + 1;

            // 必要人数に達した場合ループ終了
            if ($remainingCount <= 0) {
                break;
            }
        }

        // 複数人申請しているシフトを処理
        foreach ($multipleRequestShifts as $requests) {
            if ($remainingCount <= 0) {
                break;
            }

            // シフト数が少ない順、同数の場合はpriority順で並べ替え
            $requests = $requests->sortBy(function ($request) use ($shiftCounts) {
                $count = $shiftCounts[$request->user_id] ?? 0;
                $priority = $this->getUserPriority($request->user_id);
                return [$count, $priority];
            })->values();

            foreach ($requests as $request) {
                if ($remainingCount <= 0) {
                    break;
                }

                if ($this->isUserDayOff($constraintShiftForDay, $infoShift->date, $request->user_id)) {
                    continue; // day_offの場合はスキップ
                }

                // ユーザーがすでにその日に別の役割を持っている場合スキップ
                if (isset($assignedUsers[$infoShift->date][$request->user_id])) {
                    continue;
                }
                // paired_withの制約を確認
                if ($this->isUserRestrictedByPairing($constraintShiftForDay, $infoShift->date, $request->user_id)) {
                    continue; // pairing制約に該当する場合はスキップ
                }

                $this->assignShift($finalShifts, $assignedUsers, $constraintShiftForDay, $request, $infoShift, $roleInfo);
                $remainingCount--;
                $shiftCounts[$request->user_id] = ($shiftCounts[$request->user_id] ?? 0) + 1;
            }
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

    /**
     * pairing制約によるシフト不可確認
     */
    private function isUserRestrictedByPairing(array $constraintShiftForDay, string $date, int $userId): bool
    {
        if (!isset($constraintShiftForDay[$date])) {
            return false; // その日に割り当てがない場合は問題なし
        }

        // paired_with制約に該当する場合
        if (isset($constraintShiftForDay[$date][$userId]) && str_starts_with($constraintShiftForDay[$date][$userId], 'paired_with_')) {
            return true;
        }

        return false;
    }
    // ペアリングされたシフトを事前に割り当てる
    private function processPairingShifts(array &$finalShifts, array &$assignedUsers, $requestedShifts, $infoShift, $rolesWithCounts, int &$remainingCount): void
    {
        foreach ($requestedShifts as $request) {
            if ($remainingCount <= 0) {
                break;
            }

            if ($this->isPairedUser($request->user_id)) {
                $pairedUserId = $this->getPairedUserId($request->user_id);

                // 新人またはバイトリーダーがday_offの場合はスキップ
                if (
                    $this->isUserDayOff($assignedUsers, $infoShift->date, $request->user_id) ||
                    $this->isUserDayOff($assignedUsers, $infoShift->date, $pairedUserId)
                ) {
                    continue;
                }
                // ユーザーがすでにその日に別の役割を持っている場合スキップ
                if (isset($assignedUsers[$infoShift->date][$request->user_id])) {
                    continue;
                }
                // 新人がバイトリーダーと共にシフトに入れるか確認
                if ($this->canPairWork($requestedShifts, $infoShift, $request->user_id, $pairedUserId)) {
                    $roleInfo = $rolesWithCounts;
                    // 新人の役割を割り当て
                    $this->assignShift($finalShifts, $assignedUsers, $constraintShiftForDay, $request, $infoShift, $roleInfo);
                    $remainingCount--;

                    // バイトリーダーの役割を割り当て
                    $this->assignShift($finalShifts, $assignedUsers, $constraintShiftForDay, (object)['user_id' => $pairedUserId], $infoShift, $roleInfo);
                    $remainingCount--;
                }
            }
        }
    }



    // 必要な補助関数
    private function isPairedUser(int $userId): bool
    {
        // ペアリング設定を持つユーザーか確認
        return ShiftConstraint::where('user_id', $userId)->whereNotNull('paired_user_id')->exists();
    }

    private function getPairedUserId(int $userId): ?int
    {
        $constraint = ShiftConstraint::where('user_id', $userId)->first();
        // dd($constraint->id);
        return $constraint ? $constraint->paired_user_id : null;
    }

    private function canPairWork($requestedShifts, $infoShift, $userId, $pairedUserId): bool
    {
        // dd($pairedUserId);
        $result = $requestedShifts->where('user_id', $userId)->where('date', $infoShift->date)->isNotEmpty()
            && $requestedShifts->where('user_id', $pairedUserId)->where('date', $infoShift->date)->isNotEmpty();

        // dd($result); // ここで確認
        return $result;
    }

    private function getSingleRequestShifts($requestedShifts, $infoShift, $roleInfo)
    {
        return $requestedShifts->filter(function ($reqShift) use ($infoShift, $roleInfo) {
            return $infoShift->date === $reqShift->date
                && $infoShift->start_time <= $reqShift->start_time
                && $infoShift->end_time >= $reqShift->end_time
                && in_array($roleInfo['role'], $this->getUserRoles($reqShift->user_id));
        })->groupBy('user_id')->filter(function ($group) {
            return count($group) === 1;
        })->flatten();
    }

    private function getMultipleRequestShifts($requestedShifts, $infoShift, $roleInfo)
    {
        return $requestedShifts->filter(function ($reqShift) use ($infoShift, $roleInfo) {
            return $infoShift->date === $reqShift->date
                && $infoShift->start_time <= $reqShift->start_time
                && $infoShift->end_time >= $reqShift->end_time
                && in_array($roleInfo['role'], $this->getUserRoles($reqShift->user_id));
        })->groupBy('user_id')->filter(function ($group) {
            return count($group) > 1;
        });
    }

    private function assignShift(array &$finalShifts, array &$assignedUsers, array &$constraintShiftForDay, $request, $infoShift, $roleInfo)
    {
        $finalShifts[] = [
            'date' => $infoShift->date,
            'start_time' => $infoShift->start_time,
            'end_time' => $infoShift->end_time,
            'user_id' => $request->user_id,
            'role' => $roleInfo['role'],
            'status' => 'processed',
        ];

        $assignedUsers[$infoShift->date][$request->user_id] = $roleInfo['role'];

        // max_shifts が存在するか確認し、current_shifts_count を更新する
        if ($constraintShiftForDay[$infoShift->date][$request->user_id] == 'max_shifts') {
            $constraintShiftForDay[$request->user_id]['max_shifts_explain']['current_shifts_count']++;

            // current_shifts_count が shift_limit に達した場合、ユーザーを permanently に assignedUsers に割り当てる
            if ($constraintShiftForDay[$request->user_id]['max_shifts_explain']['current_shifts_count'] >= $constraintShiftForDay[$request->user_id]['max_shifts_explain']['shift_limit']) {
                foreach ($constraintShiftForDay[$request->user_id]['max_shifts_explain']['dates'] as $applicable_day) {
                    $assignedUsers[$applicable_day][$request->user_id] = 'max_shifts_reached';
                }
            }
        }
        // no_pairing のチェックと追加入力
        // ここでnopairingが割り当てられているときの処理をしている
        if (
            isset($constraintShiftForDay[$infoShift->date][$request->user_id]) &&
            strpos($constraintShiftForDay[$infoShift->date][$request->user_id], 'no_pairing_with_') === 0
        ) {
            $noPairingUserId = (int)str_replace('no_pairing_with_', '', $constraintShiftForDay[$infoShift->date][$request->user_id]);
            $assignedUsers[$infoShift->date][$noPairingUserId] = 'no_pairing_restricted';
        }
    }



    // この下の関数はユーザーが休みになったときにユーザーをシフトの申請から除外する処理です
    private function isUserDayOff(array $constraintShiftForDay, string $date, $userId): bool
    {
        return isset($constraintShiftForDay[$date][$userId]) && $constraintShiftForDay[$date][$userId] === 'day_off';
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
