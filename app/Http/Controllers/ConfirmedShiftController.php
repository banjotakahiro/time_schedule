<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfirmedShiftRequest;
use App\Http\Requests\UpdateConfirmedShiftRequest;
use App\Models\ConfirmedShift;
use Illuminate\Http\Request;
use App\Calendar\CalendarGenerator;
use App\Calendar\MonthDaysShow;
use App\Calendar\ConfirmedCalendar;

class ConfirmedShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 現在の日時を基準にカレンダーを生成
        $date = json_decode($request->input('date'), true);
        $calendar = new CalendarGenerator($date);

        // Userと紐づいているRequested_shiftsテーブルの処理はWeekDaysShow.phpで行っているため
        // 下に記載してある返り値のshow_scheduleと一緒に格納されている
        // リクエストから基準日を取得（デフォルトは現在日時）
        $date = json_decode($request->input('date'), true);
        // CalendarGeneratorを初期化
        $calendar = new CalendarGenerator($date);

        // クエリパラメータ 'action' を取得
        $action = $request->query('action');

        // アクションに応じた処理を実行
        if ($action === 'nextmonth') {
            // 実行したい関数を呼び出す
            $month = $calendar->nextMonth();
        } elseif ($action == 'previousmonth') {
            $month = $calendar->previousMonth();
        } else {
            $month = $calendar->getCurrentMonth();
        }
        // 配列から 'start' キーの Carbon オブジェクトを取得
        $start = $month['start'];

        // 'start' の Carbon オブジェクトを 'Y-m' 形式で整形
        $formattedMonth = $start->format('Y-m');

        // ConfirmedShiftの関数を呼び出してシフト表を取得
        $create_shifts = new ConfirmedCalendar($formattedMonth);

        $final_shifts = $create_shifts->generateShiftPlan(); // 2024年12月のシフト表を生成

        dd($final_shifts);
        // 月のデータを取得
        $month_days_show = new MonthDaysShow();
        $show_month_schedule = $month_days_show->show_month_schedule($calendar->getCurrentMonth());
        // ビューにデータを渡す
        return view('confirmed_shifts.index', [
            'currentMonth' => $month,
            'show_month_schedule' => $show_month_schedule,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConfirmedShiftRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ConfirmedShift $confirmedShift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConfirmedShift $confirmedShift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConfirmedShiftRequest $request, ConfirmedShift $confirmedShift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConfirmedShift $confirmedShift)
    {
        //
    }
}
