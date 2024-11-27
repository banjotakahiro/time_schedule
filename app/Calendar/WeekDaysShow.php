<?php

namespace App\Calendar;

use Carbon\Carbon;
// use App\Models\Requested_shift;
use App\Models\User;

// ここでは、一週間の日付をカラムに表示させることと
// ユーザーが登録したシフトデータを表示させる処理を行っています
class WeekDaysShow
{
    public function showSchedule($currentWeek)
    {
        // 現在の週の開始日と終了日
        $startOfWeek = $currentWeek['start']->copy();
        $dayOfWeek = ["月", "火", "水", "木", "金", "土", "日"];

        // 各日付を生成
        $weekDays = [];
        foreach (range(0, 6) as $day) {
            $weekDays[] = $startOfWeek->copy()->addDays($day)->format('Y-m-d'); // 'Y-m-d' 形式で文字列を格納
        }

        // ユーザーとシフトを取得
        $users = User::with('requestedShifts')->get();

        // 各ユーザーに関連付けられたデータを整形
        $userSchedules = [];
        foreach ($users as $user) {
            $schedule = [];
            foreach ($weekDays as $date) { // $date は 'Y-m-d' 形式の文字列
                // 該当日のシフトをすべて取得
                $shifts = $user->requestedShifts->filter(function ($shift) use ($date) {
                    return Carbon::parse($shift->start)->format('Y-m-d') === $date; // 日付を文字列で比較
                });

                // 日付をキー、シフトを配列で格納
                $schedule[$date] = $shifts->isNotEmpty()
                    ? $shifts->pluck('title')->toArray() // シフトタイトルを取得
                    : ['シフトなし']; // シフトがなければ 'シフトなし'
            }

        // スケジュールデータを格納
            $userSchedules[] = [
                'name' => $user->name,
                'user_id' => $user->id,
                'schedule' => $schedule,
            ];
        }


        // データをビューに渡す
        return [
            'dayOfWeek' => $dayOfWeek,
            'weekDays' => $weekDays,
            'userSchedules' => $userSchedules,
        ];
    }
}
