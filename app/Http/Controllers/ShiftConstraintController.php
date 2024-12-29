<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\ShiftConstraint;
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
    public function store(Request $request)
    {
        $shiftConstraint = new ShiftConstraint();
        $shiftConstraint->status = $request->input('status');
        $shiftConstraint->user_id = $request->input('user_id');
        $shiftConstraint->date = $request->input('date');
        $shiftConstraint->paired_user_id = $request->input('paired_user_id');
        $shiftConstraint->max_shifts = $request->input('max_shifts');
        $shiftConstraint->role = $request->input('role');
        $shiftConstraint->priority = $request->input('priority');
        $shiftConstraint->extra_info = $request->input('extra_info');
        $shiftConstraint->save();

        return response()->json([
            'shiftConstaraint' => $shiftConstraint
        ]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShiftConstraint $shiftConstraint)
    {
        Log::info('Received data for store:', $request->all());

        $shiftConstraint->status = $request->input('status');
        $shiftConstraint->user_id = $request->input('user-id');
        $shiftConstraint->date = $request->input('date');
        $shiftConstraint->paired_user_id = $request->input('paired-user-id');
        $shiftConstraint->max_shifts = $request->input('max-shifts');
        $shiftConstraint->role = $request->input('role');
        $shiftConstraint->priority = $request->input('priority');
        $shiftConstraint->extra_info = $request->input('extra-info');
        $shiftConstraint->save();

        return response()->json([
            'shiftConstraint' => $shiftConstraint
        ]);
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
