<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInformation_shiftRequest;
use App\Http\Requests\UpdateInformation_shiftRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Information_shift;
use App\Models\Role;

use App\Calendar\CalendarGenerator;
use App\Calendar\MonthDaysShow;

class InformationShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 現在の日時を基準にカレンダーを生成
        $date = json_decode($request->input('date'), true);
        $calendar = new CalendarGenerator($date);
        $information_shift = Information_shift::all();

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

        // 月のデータを取得
        $month_days_show = new MonthDaysShow();
        $show_month_schedule = $month_days_show->show_month_schedule($calendar->getCurrentMonth());
        // ビューにデータを渡す
        return view('information_shifts.index', [
            'currentMonth' => $month,
            'show_month_schedule' => $show_month_schedule,
            'information_shift' => $information_shift,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // どんな役職があるかの値を受け取る
        $roles = Role::all();

        // クエリパラメータ 'date' を取得し、デフォルトで今日の日付を設定
        $date = $request->query('date', now()->format('Y-m-d'));

        // 該当日付のシフトデータを取得
        $existingShift = Information_shift::where('date', $date)->first();

        // ビューにデータを渡す
        return view('information_shifts.create', [
            'date' => $date,
            'roles' => $roles,
            'existingShift' => $existingShift,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInformation_shiftRequest $request)
    {

        // リクエストから値を取得
        $date = $request->date; // 開始日
        $endDate = $request->end_date; // 終了日
        $startTime = $request->start_time; // 勤務開始時刻
        $endTime = $request->end_time; // 勤務終了時刻
        $location = $request->location; // 勤務場所
        $color = $request->color; // 色
        $role1 = $request->role1; // 必要スキル1
        $requiredStaffRole1 = $request->required_staff_role1; // スキル1の必要人数
        $role2 = $request->role2; // 必要スキル2
        $requiredStaffRole2 = $request->required_staff_role2; // スキル2の必要人数
        $role3 = $request->role3; // 必要スキル3
        $requiredStaffRole3 = $request->required_staff_role3; // スキル3の必要人数



        // Carbonインスタンスを生成
        $currentDate = Carbon::parse($date);
        $endDate = Carbon::parse($endDate);

        // 日付範囲をループで処理
        while ($currentDate->lte($endDate)) {
            // 重複データを検出して更新または作成
            Information_shift::updateOrCreate(
                ['date' => $currentDate->format('Y-m-d')], // 条件
                [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'location' => $location,
                    'color' => $color,
                    'role1' => $role1,
                    'required_staff_role1' => $requiredStaffRole1,
                    'role2' => $role2,
                    'required_staff_role2' => $requiredStaffRole2,
                    'role3' => $role3,
                    'required_staff_role3' => $requiredStaffRole3,
                ]
            );

            // 次の日へ進む
            $currentDate->addDay();
        }

        // 処理結果を返す
        return redirect()->route('information_shifts.index')
            ->with('success', __('Shifts created or updated successfully for the date range.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Information_shift $information_shift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Information_shift $information_shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInformation_shiftRequest $request, Information_shift $information_shift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Information_shift $information_shift) {}
}
