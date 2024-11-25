<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequestedShiftRequest;
use App\Http\Requests\UpdateRequestedShiftRequest;
use Illuminate\Http\Request;

use App\Models\Requested_shift;


class RequestedShiftController extends Controller
{
    // indexページへ移動
    public function index()
    {
        $request_shifts = Requested_shift::all();
        return view('requested_shifts.index', ['requested_shifts' => $request_shifts]);
    }
    public function show($id)
    {
        $requested_shift = Requested_shift::find($id);
        return view('requested_shifts.show', ['requested_shift' => $requested_shift]);
    }

    public function create() 
    {
        return view('required_shifts.create');
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
