<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => '接客',
                'description' => '顧客への対応やサポートを行う業務',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '清掃',
                'description' => '施設や設備の清掃を行う業務',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '調理補助',
                'description' => 'キッチンでの調理補助や食材の準備を行う業務',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '運搬',
                'description' => '荷物や資材の運搬作業を行う業務',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
