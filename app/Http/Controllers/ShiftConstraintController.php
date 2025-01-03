<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\ShiftConstraint;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class ShiftConstraintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    //  ここはややこしいけどインプットの値がstoreのときはアンダーバーだがupdateのときは-になっている
    public function store(Request $request)
    {
        $shiftConstraint = new ShiftConstraint();
        $shiftConstraint->status = $request->input('status');
        $shiftConstraint->user_id = $request->input('user_id');
        $shiftConstraint->start_date = $request->input('start_date');
        $shiftConstraint->end_date = $request->input('end_date');
        $shiftConstraint->paired_user_id = $request->input('paired_user_id');
        $shiftConstraint->max_shifts = $request->input('max_shifts');
        $shiftConstraint->role = $request->input('role');
        $shiftConstraint->priority = $request->input('priority');
        $shiftConstraint->extra_info = $request->input('extra_info');
        $shiftConstraint->save();

        return redirect()->route('confirmed_shifts.index')->with('success', 'シフト設定を更新しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(ShiftConstraint $shiftConstraint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShiftConstraint $shiftConstraint)
    {
        // 必要なデータを取得
        $users = User::all(); // ユーザー一覧
        $roles = Role::all(); // 役割一覧

        // edit.blade.php にデータを渡してビューを返す
        return view('shift_constraints.edit', [
            'shift_constraint' => $shiftConstraint,
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShiftConstraint $shiftConstraint)
    {
        Log::info('Received data for store:', $request->all());

        $shiftConstraint->status = $request->status;
        $shiftConstraint->user_id = $request->user_id;
        $shiftConstraint->start_date = $request->start_date;
        $shiftConstraint->end_date = $request->end_date;
        $shiftConstraint->paired_user_id = $request->paired_user_id;
        $shiftConstraint->max_shifts = $request->max_shifts;
        $shiftConstraint->role = $request->role;
        $shiftConstraint->priority = $request->priority;
        $shiftConstraint->extra_info = $request->extra_info;
        $shiftConstraint->save();

        // リダイレクト処理
        return redirect()->route('confirmed_shifts.index')->with('success', 'シフト設定を更新しました。');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $shift_constraint = ShiftConstraint::find($id);
        $shift_constraint->delete();

        return redirect('/confirmed_shifts');
    }
}
