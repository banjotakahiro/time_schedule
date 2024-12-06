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
        $update_skills= $request->update_skills;
        $employee->skill1 = $update_skills["skill1"];
        $employee->skill2 = $update_skills["skill2"];
        $employee->skill3 = $update_skills["skill3"];
        $employee->save();
        // 必ずJSON形式のレスポンスを返す
        return response()->json([
            'success' => true,
            'employee' => $employee
        ]);
    }

    public function update(Request $request, $employee_Id)
    {
        $employee = Employee::find($employee_Id);
        $employee->notes = $request->notes;
        $employee->user_id = $request->user_id;
        $update_skills= $request->update_skills;
        $employee->skill1 = $update_skills["skill1"];
        $employee->skill2 = $update_skills["skill2"];
        $employee->skill3 = $update_skills["skill3"];
        $employee->save();

            // 必ずJSON形式のレスポンスを返す
    return response()->json([
        'success' => true,
        'employee' => $employee
    ]);
    }
}
