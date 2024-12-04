<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 初期データの挿入
        DB::table('employees')->insert([
            [
                'user_id' => 1, // usersテーブルのIDを参照
                'skill1' => 6,  // rolesテーブルのIDを参照
                'skill2' => 7,  // rolesテーブルのIDを参照
                'skill3' => null, // スキル3は設定しない
                'notes' => '週末のみ勤務可能',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2, // usersテーブルのIDを参照
                'skill1' => 12,  // rolesテーブルのIDを参照
                'skill2' => 5,
                'skill3' => null,
                'notes' => 'フルタイム勤務可能',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3, // usersテーブルのIDを参照
                'skill1' => 5,  // rolesテーブルのIDを参照
                'skill2' => 6,  // rolesテーブルのIDを参照
                'skill3' => 7,  // rolesテーブルのIDを参照
                'notes' => '早朝シフト希望',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
