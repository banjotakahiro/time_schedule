<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // indexページへ移動

   public function index()
   {
       // モデル名::テーブル全件取得
        $roles = Role::all();
        return view('roles.index',['roles' => $roles]);
   }

   public function destroy($id)
   {
        $role = Role::find($id);
        $role ->delete();

        return redirect('/roles');
   }
}
