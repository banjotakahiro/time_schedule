<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        $employee = new Employee();
        $employee->notes = $request->notes;
        $employee->user_id = $request->user_id;
        $employee->save();
        // 必ずJSON形式のレスポンスを返す
        return response()->json([
            'success' => true,
        ]);
    }

    public function update(Request $request, $employee_Id)
    {
        $employee = Employee::find($employee_Id);
        $employee->notes = $request->notes;
        $employee->user_id = $request->user_id;

        $employee->save();

            // 必ずJSON形式のレスポンスを返す
    return response()->json([
        'success' => true,
    ]);
    }
}
