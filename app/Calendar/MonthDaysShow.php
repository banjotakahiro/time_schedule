<?php

namespace App\Calendar;

use Carbon\Carbon;
use App\Models\Information_shift;

// ここでは、一週間の日付をカラムに表示させることと
// ユーザーが登録したシフトデータを表示させる処理を行っています
class MonthDaysShow
{
    public function show_month_schedule($currentMonth)
    {
        // 月の開始日と終了日を取得
        $startOfMonth = $currentMonth['start']->copy();
        $endOfMonth = $currentMonth['end']->copy();


        // 週ごとのデータを格納する配列
        $monthWeeks = [];
        $currentWeek = [];

        // 月初を日曜日に調整
        $adjustedStartOfMonth = $startOfMonth->copy();
        while (!$adjustedStartOfMonth->isSunday()) {
            $adjustedStartOfMonth->subDay();
        }

        $currentWeek = [];
        $monthWeeks = [];

        // 修正された開始日から終了条件を次の月の最初の日曜日までに変更
        $adjustedEndOfMonth = $endOfMonth->copy();
        while (!$adjustedEndOfMonth->isSunday()) {
            $adjustedEndOfMonth->addDay();
        }

        // 修正された開始日からループを開始
        while ($adjustedStartOfMonth <= $adjustedEndOfMonth) {
            // 現在の日付を現在の週に追加
            $currentWeek[] = $adjustedStartOfMonth->copy()->format('Y-m-d');

            // 土曜日または修正された月末に達した場合、現在の週を週リストに追加
            if ($adjustedStartOfMonth->isSaturday() || $adjustedStartOfMonth->eq($adjustedEndOfMonth)) {
                $monthWeeks[] = $currentWeek;
                $currentWeek = []; // 新しい週を準備
            }

            $adjustedStartOfMonth->addDay(); // 次の日に進む
        }

        // データをビューに渡す
        return [
            'monthWeeks' => $monthWeeks,
        ];



        // // ユーザーとシフトを取得
        // $Information_shifts = Information_shift::all();

        // // 各ユーザーに関連付けられたデータを整形
        // $userSchedules = [];
        // foreach ($users as $user) {
        //     $schedule = [];
        //     foreach ($MonthDays as $date) { // $date は 'Y-m-d' 形式の文字列
        //         // 該当日のシフトをすべて取得
        //         $shifts = $user->requestedShifts->filter(function ($shift) use ($date) {
        //             return Carbon::parse($shift->start)->format('Y-m-d') === $date; // 日付を文字列で比較
        //         });

        //         // 日付をキー、シフトを配列で格納
        //         $schedule[$date] = $shifts->isNotEmpty()
        //             ? $shifts->pluck('title')->toArray() // シフトタイトルを取得
        //             : ['シフトなし']; // シフトがなければ 'シフトなし'
        //     }

        // // スケジュールデータを格納
        //     $userSchedules[] = [
        //         'name' => $user->name,
        //         'user_id' => $user->id,
        //         'schedule' => $schedule,
        //     ];
        // }


        // // データをビューに渡す
        // return [
        //     'dayOfMonth' => $dayOfMonth,
        //     'MonthDays' => $MonthDays,
        //     'userSchedules' => $userSchedules,
        // ];
    }
}
