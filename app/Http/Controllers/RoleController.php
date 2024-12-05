<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
     // indexページへ移動

     public function index()
     {
          // モデル名::テーブル全件取得
          $users = User::with('employee')->get();
          $roles = Role::all();
          return view('roles.index', ['users' => $users, 'roles' => $roles]);
     }

     public function store(StoreRoleRequest $request)
     {
          // データベースに保存
             // インスタンスの作成
             $role = new Role;

             // 値の用意
             $role->name = $request->name;
             $role->description = $request->description;

             // インスタンスに値を設定して保存
             $role->save();

          // 成功レスポンスを返す
          return response()->json([
               'success' => true,
               'message' => '新しい役割が作成されました',
               'data' => $role,
          ]);
     }


     public function update(UpdateRoleRequest $request, $roleId)
     {
          // デバッグ用: リクエストデータの確認
          try {
               // 本来の処理
               $role = Role::findOrFail($roleId);
               $role->name = $request->name;
               $role->description = $request->description;
               $role->save();
          } catch (\Exception $e) {
               dd($e->getMessage(), $e->getTrace());
          }

          return response()->json(['success' => true, 'message' => '役割が更新されました。']);
     }


     public function destroy($id)
     {
          $role = Role::find($id);
          $role->delete();

          return redirect('/roles');
     }
}
