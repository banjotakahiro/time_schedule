<?php

use App\Http\Controllers\RequestedShiftController;
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

