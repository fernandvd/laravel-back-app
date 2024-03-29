<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;

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


Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store'])->name('login.store');
});

Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');

//dash
Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

//user
Route::name('users.')->middleware('auth')->group(function () {
    Route::get('users', [UserController::class, 'index'])
        ->name('list');
    Route::get('users/create', [UserController::class, 'create'])->name('create');
    Route::post('users', [UserController::class, 'store'])->name('store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('destroy');
});
