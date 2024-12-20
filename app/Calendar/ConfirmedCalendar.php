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
            // 各役割と必要人数を取得
            $rolesWithCounts = [
                ['role' => $infoShift->role1, 'count' => $infoShift->required_staff_role1],
                ['role' => $infoShift->role2, 'count' => $infoShift->required_staff_role2],
                ['role' => $infoShift->role3, 'count' => $infoShift->required_staff_role3],
            ];
            


            foreach ($rolesWithCounts as $roleInfo) {
                if (!$roleInfo['role'] || $roleInfo['count'] <= 0) {
                    continue;
                }

                $requestsForRole = $requestedShifts->filter(function ($reqShift) use ($infoShift, $roleInfo) {
                    return $infoShift->date === $reqShift->date
                        && $infoShift->start_time <= $reqShift->start_time
                        && $infoShift->end_time >= $reqShift->end_time
                        && in_array($roleInfo['role'], $this->getUserRoles($reqShift->user_id));
                });

                $remainingCount = $roleInfo['count'];

                if ($requestsForRole->isEmpty()) {
                    for ($i = 0; $i < $remainingCount; $i++) {
                        $finalShifts[] = [
                            'date' => $infoShift->date,
                            'start_time' => $infoShift->start_time,
                            'end_time' => $infoShift->end_time,
                            'user_id' => null,
                            'role' => $this->getRoleNameById($roleInfo['role']),
                            'status' => 'no_applicant',
                        ];
                    }
                    continue;
                }

                $selectedUsers = $requestsForRole->pluck('user_id')->take($remainingCount);

                foreach ($selectedUsers as $userId) {
                    $finalShifts[] = [
                        'date' => $infoShift->date,
                        'start_time' => $infoShift->start_time,
                        'end_time' => $infoShift->end_time,
                        'user_id' => $userId,
                        'role' => $this->getRoleNameById($roleInfo['role']),
                        'status' => 'confirmed',
                    ];
                    $remainingCount--;

                    if ($remainingCount <= 0) {
                        break;
                    }
                }

                // 必要人数を満たせなかった場合
                for ($i = 0; $i < $remainingCount; $i++) {
                    $finalShifts[] = [
                        'date' => $infoShift->date,
                        'start_time' => $infoShift->start_time,
                        'end_time' => $infoShift->end_time,
                        'user_id' => null,
                        'role' => $this->getRoleNameById($roleInfo['role']),
                        'status' => 'insufficient',
                    ];
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

    /**
     * 役割名を取得するメソッド
     *
     * @param int $roleId
     * @return string
     */
    private function getRoleNameById(int $roleId): string
    {
        $role = Role::find($roleId);

        return $role ? $role->name : 'Unknown Role';
    }
}
