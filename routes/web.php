<?php

use App\Http\Controllers\RequestedShiftController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InformationShiftController;
use App\Http\Controllers\ConfirmedShiftController;
use App\Http\Controllers\ShiftConstraintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('root');

// ログインしてない人はログイン画面にとび、ログインした人はdashboardに飛ぶというルーティング設定
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// ログイン用のルーティング設定
Route::get('company/register', function () {
    return view('company.register');
})->middleware('guest')
    ->name('company.register');

// シフトのデータを登録するCRUD用のルーティング
Route::resource('requested_shifts', RequestedShiftController::class)
    ->middleware('auth');

// どんな仕事があるかの仕事のデータを登録するためのCRUD用のルーティング
Route::resource('roles', RoleController::class)
    ->middleware('auth');

// ユーザー情報にできる仕事や場所を設定するためのルーティング。上の仕事用のルーティングと同じページに記載
Route::resource('employees', EmployeeController::class)
    ->middleware('auth');

Route::resource('information_shifts', InformationShiftController::class)
    ->middleware('auth');

Route::resource('confirmed_shifts', ConfirmedShiftController::class)
    ->middleware('auth');

Route::resource('shift_constraints', ShiftConstraintController::class)
    ->middleware('auth');
