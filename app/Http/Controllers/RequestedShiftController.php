<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Requested_shift;


class RequestedShiftController extends Controller
{
    // indexページへ移動
    public function index()
    {
        $request_shifts = Requested_shift::all();
        return view( 'requested_shifts.index' , ['requested_shifts' => $request_shifts]);
    }
    public function show($id)
    {
        $requested_shift = Requested_shift::find($id);
        return view('requested_shifts.show', ['requested_shift' => $requested_shift]);
    }
    
    public function edit($id)
    {
        $requested_shift = Requested_shift::find($id);
        return view('requested_shifts.edit' ,['requested_shift' => $requested_shift]);
    }

    public function create()
    {
    
    }

    public function store()
    {
    
    }

    public function delete()
    {
    }

}
