<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RequestedShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // 日付範囲（12月10日から12月21日）
        $dates = [];
        for ($i = 10; $i <= 21; $i++) {
            $dates[] = Carbon::create(2024, 12, $i)->format('Y-m-d');
        }

        // データを挿入
        foreach ($dates as $date) {
            DB::table('requested_shifts')->insert([
                'user_id' => rand(1, 6), // 1から6のランダムな値
                'date' => $date,
                'start_time' => '20:00:00',
                'end_time' => '22:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
