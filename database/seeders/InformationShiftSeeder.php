<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InformationShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // ランダムな数値を生成するヘルパー関数
        $randomSkill = function () {
            return rand(5, 8);
        };

        // サンプルデータを挿入
        DB::table('information_shifts')->insert([
            [
                'date' => '2024-12-08',
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'location' => 'Office A',
                'skill1' => $randomSkill(),
                'required_staff_skill1' => rand(1, 5),
                'skill2' => $randomSkill(),
                'required_staff_skill2' => rand(1, 3),
                'skill3' => null, // 3つ目のスキルは省略可能
                'required_staff_skill3' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'date' => '2024-12-09',
                'start_time' => '10:00:00',
                'end_time' => '19:00:00',
                'location' => null, // 場所が未定の場合
                'skill1' => $randomSkill(),
                'required_staff_skill1' => rand(1, 4),
                'skill2' => $randomSkill(),
                'required_staff_skill2' => rand(1, 2),
                'skill3' => $randomSkill(),
                'required_staff_skill3' => rand(1, 3),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'date' => '2024-12-10',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'location' => 'Warehouse B',
                'skill1' => $randomSkill(),
                'required_staff_skill1' => rand(1, 3),
                'skill2' => null, // 2つ目のスキルは省略可能
                'required_staff_skill2' => null,
                'skill3' => $randomSkill(),
                'required_staff_skill3' => rand(1, 2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
