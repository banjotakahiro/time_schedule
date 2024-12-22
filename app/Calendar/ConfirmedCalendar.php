<?php

namespace App\Calendar;

use App\Models\Employee;
use App\Models\Requested_shift;
use App\Models\Information_shift;
use App\Models\Role;
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
    public function generateShiftPlan(): array
    {
        $finalShifts = []; // 最終的なシフト表
        $assignedUsers = []; // 既に割り当てられたユーザー

        // 指定された月のリクエストシフトと情報シフトを取得
        $requestedShifts = Requested_shift::where('date', 'like', "{$this->month}%")->get();
        $informationShifts = Information_shift::where('date', 'like', "{$this->month}%")->get();

        foreach ($informationShifts as $infoShift) {
            $rolesWithCounts = [
                ['role' => $infoShift->role1, 'count' => $infoShift->required_staff_role1],
                ['role' => $infoShift->role2, 'count' => $infoShift->required_staff_role2],
                ['role' => $infoShift->role3, 'count' => $infoShift->required_staff_role3],
            ];

            foreach ($rolesWithCounts as $roleInfo) {
                if (!$roleInfo['role'] || $roleInfo['count'] <= 0) {
                    continue;
                }

                $requestsForRole = $requestedShifts->filter(function ($reqShift) use ($infoShift, $roleInfo, $assignedUsers) {
                    return $infoShift->date === $reqShift->date
                        && $infoShift->start_time <= $reqShift->start_time
                        && $infoShift->end_time >= $reqShift->end_time
                        && in_array($roleInfo['role'], $this->getUserRoles($reqShift->user_id))
                        && !in_array($reqShift->user_id, $assignedUsers); // 割り当て済みユーザーを除外
                });

                $remainingCount = $roleInfo['count'];

                if ($requestsForRole->count() === 1) {
                    // 一人だけ申請がある場合
                    $singleRequest = $requestsForRole->first();
                    $finalShifts[] = [
                        'date' => $infoShift->date,
                        'start_time' => $infoShift->start_time,
                        'end_time' => $infoShift->end_time,
                        'user_id' => $singleRequest->user_id,
                        'role' => $roleInfo['role'],
                        'status' => 'confirmed_single',
                    ];
                    $assignedUsers[] = $singleRequest->user_id; // ユーザーを割り当て済みに追加
                } elseif ($requestsForRole->count() > 1) {
                    // 申請が二人以上ある場合、priorityを考慮してソート
                    $sortedRequests = $requestsForRole->sort(function ($a, $b) {
                        $priorityA = Employee::where('user_id', $a->user_id)->value('priority') ?? 0;
                        $priorityB = Employee::where('user_id', $b->user_id)->value('priority') ?? 0;

                        return $priorityB <=> $priorityA; // priority降順でソート
                    });

                    foreach ($sortedRequests as $request) {
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
                        $assignedUsers[] = $request->user_id; // ユーザーを割り当て済みに追加
                        $remainingCount--;
                    }
                } else {
                    // 必要人数を満たせなかった場合
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
        }

            
        return $finalShifts;
    }


    /**
     * ユーザーの役割を取得するメソッド
     *
     * @param int $userId
     * @return array
     */
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
