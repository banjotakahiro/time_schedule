<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequestedShiftRequest;
use App\Http\Requests\UpdateRequestedShiftRequest;
use Illuminate\Http\Request;

use App\Models\Requested_shift;
use App\Models\User;
use App\Calendar\CalendarGenerator;


class RequestedShiftController extends Controller
{
    // indexページへ移動
    public function index(Request $request)
    {
        // 全てのユーザーとその関連するrequestedShiftsを取得
        $users = User::with('RequestedShifts')->get();
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
        
        return view('requested_shifts.index', [
            'currentWeek' => $week,
            'users' => $users,
        ]);
    }
    public function show($id)
    {
        $requested_shift = Requested_shift::find($id);
        return view('requested_shifts.show', ['requested_shift' => $requested_shift]);
    }

    public function create() 
    {
        return view('requested_shifts.create');
    }

    public function store(StoreRequestedShiftRequest $request) 
    {
        $requested_shift = new Requested_shift;

        $requested_shift->start = $request->start;
        $requested_shift->end = $request->end;
        $requested_shift->title = $request->title;
        $requested_shift->body = $request->body;

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
