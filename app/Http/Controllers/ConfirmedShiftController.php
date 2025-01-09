<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfirmedShiftRequest;
use App\Http\Requests\UpdateConfirmedShiftRequest;
use App\Models\ConfirmedShift;
use App\Models\ShiftConstraint;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Calendar\CalendarGenerator;
use App\Calendar\MonthDaysShow;
use App\Calendar\ConfirmedCalendar;

use Carbon\Carbon;

class ConfirmedShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Userと紐づいているRequested_shiftsテーブルの処理はWeekDaysShow.phpで行っているため
        // 下に記載してある返り値のshow_scheduleと一緒に格納されている
        // リクエストから基準日を取得（デフォルトは現在日時）
        // JSONデータをデコード
        $month = json_decode($request->input('date'), true);

        // $month が null の場合は処理をスキップ
        if ($month !== null) {
            // 日本時間に変換
            $month['start'] = Carbon::parse($month['start'])->setTimezone('Asia/Tokyo');
            $month['end'] = Carbon::parse($month['end'])->setTimezone('Asia/Tokyo');
        }
        // CalendarGeneratorを初期化
        $calendar = new CalendarGenerator($month);
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
        $confirmed_shifts = ConfirmedShift::all();
        $shift_constraints = ShiftConstraint::with([
            'roleDetails:id,name',
            'users:id,name',
            'paired_users:id,name',
        ])->get();
        $users = User::select('id', 'name')->get();
        $roles = Role::select('id', 'name')->get();
        $users_find_id = User::pluck('name', 'id');
        $roles_find_id = Role::pluck('name', 'id');
        // $confirmed_shifts = ConfirmedShift::all();
        // 月のデータを取得
        $month_days_show = new MonthDaysShow();
        $show_month_schedule = $month_days_show->show_month_schedule($calendar->getCurrentMonth());
        // ビューにデータを渡す
        return view('confirmed_shifts.index', [
            'currentMonth' => $month,
            'show_month_schedule' => $show_month_schedule,
            'confirmed_shifts' => $confirmed_shifts,
            'shift_constraints' => $shift_constraints,
            'users' => $users,
            'roles' => $roles,
            'users_find_id' => $users_find_id,
            'roles_find_id' => $roles_find_id,
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
    public function store(Request $request)
    {
        $month = json_decode($request->input('date'), true);
        $month['start'] = Carbon::parse($month['start'])->setTimezone('Asia/Tokyo');
        $month['end'] = Carbon::parse($month['end'])->setTimezone('Asia/Tokyo');
        $start = $month['start'];
        $formattedMonth = Carbon::parse($start)->format('Y-m');

        // Carbonで月の開始日と終了日を取得
        $startOfMonth = Carbon::parse($formattedMonth)->startOfMonth(); // 月の開始日
        $endOfMonth = Carbon::parse($formattedMonth)->endOfMonth();     // 月の終了日

        // 対象月の既存データを削除
        ConfirmedShift::whereBetween('date', [$startOfMonth, $endOfMonth])->delete();

        // シフト表を生成
        $create_shifts = new ConfirmedCalendar($formattedMonth);
        $final_shifts = $create_shifts->generateShiftPlan(); // 2024年12月のシフト表を生成

        // シフトデータを挿入
        $confirmed_shift = new ConfirmedShift;
        foreach ($final_shifts as $final_shift) {
            // 新しい ConfirmedShift モデルのインスタンスを作成
            $confirmed_shift = new ConfirmedShift();

            // データをモデルのプロパティにセット
            $confirmed_shift->user_id = $final_shift['user_id'];
            $confirmed_shift->status = $final_shift['status'];
            $confirmed_shift->role_id = $final_shift['role'];
            $confirmed_shift->date = $final_shift['date'];
            $confirmed_shift->start_time = $final_shift['start_time'];
            $confirmed_shift->end_time = $final_shift['end_time'];

            // 保存
            $confirmed_shift->save();
        }

        // modalの処理をしたい場合は下の処理を消したほうがいいです。
        // でも、redirectができないです。

        return redirect('/confirmed_shifts');
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
    public function destroy(ConfirmedShift $confirmedShift) {}
}
