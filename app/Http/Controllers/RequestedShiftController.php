<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequestedShiftRequest;
use App\Http\Requests\UpdateRequestedShiftRequest;
use Illuminate\Http\Request;

use App\Models\Requested_shift;
use App\Models\User;
use App\Calendar\CalendarGenerator;
use App\Calendar\WeekDaysShow;

class RequestedShiftController extends Controller
{
    // indexページへ移動
    public function index(Request $request)
    {
        // Userと紐づいているRequested_shiftsテーブルの処理はWeekDaysShow.phpで行っているため
        // 下に記載してある返り値のshow_scheduleと一緒に格納されている
        // リクエストから基準日を取得（デフォルトは現在日時）
        $date = json_decode($request->input('date'), true);
        // CalendarGeneratorを初期化
        $calendar = new CalendarGenerator($date);

        // クエリパラメータ 'action' を取得
        $action = $request->query('action');
        
        // アクションに応じた処理を実行
        if ($action === 'nextweek') {
            // 実行したい関数を呼び出す
            $week = $calendar->nextWeek();
        }elseif ($action == 'previousweek') {
            $week = $calendar->previousWeek();
        }
        else {
            $week = $calendar-> getCurrentWeek();
        }

        $week_days_show = new WeekDaysShow() ;
        $show_schdule = $week_days_show -> showSchedule($week);
        
        return view('requested_shifts.index', [
            'currentWeek' => $week,
            'show_schedule' => $show_schdule,
        ]);
    }
    public function show($id)
    {
        $requested_shift = Requested_shift::find($id);
        return view('requested_shifts.show', ['requested_shift' => $requested_shift]);
    }

    public function create(Request $request) 
    {
        // パラメータを受け取る
        $date = $request->query('date'); // クエリパラメータ 'date' を取得
        $user_id = $request->query('user_id'); // クエリパラメータ 'user_id' を取得
        // ここでは単純にビューにデータを渡します
        return view('requested_shifts.create', compact('date', 'user_id'));
    }

    public function store(StoreRequestedShiftRequest $request) 
    {
        $requested_shift = new Requested_shift;

        $requested_shift->start = $request->start;
        $requested_shift->end = $request->end;
        $requested_shift->title = $request->title;
        $requested_shift->body = $request->body;
        $requested_shift->user_id = $request->query('user_id');
        // 保存
        $requested_shift->save();

        // 登録したらindexに戻る
        return redirect('/requested_shifts');

    }

    public function edit($id)
    {
        $requested_shift = Requested_shift::find($id);
        return view('requested_shifts.edit', ['requested_shift' => $requested_shift]);
    }

    public function update(UpdateRequestedShiftRequest $request, $id)
    {
        $requested_shift = Requested_shift::find($id);

        $requested_shift->start = $request->start;
        $requested_shift->end = $request->end;
        $requested_shift->title = $request->title;
        $requested_shift->body = $request->body;

        // 保存
        $requested_shift->save();

        // 登録したらindexに戻る
        return redirect('/requested_shifts');
    }

    public function destroy($id)
    {
        $requested_shift = Requested_shift::find($id);
        $requested_shift->delete();
        return redirect('/requested_shifts');
    }


}
