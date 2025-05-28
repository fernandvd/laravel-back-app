<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Web\ContactsController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\OrganizationsController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Http\Request;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use Illuminate\Support\Facades\Storage;

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
    Route::put('users/{user}/restore', [UserController::class, 'restore'])->name('restore');
});

Route::controller(ContactsController::class)->name('contacts.')->middleware(['auth'])->group(function () {
    Route::get('contacts', 'index')->name('index');
    Route::get('contacts/create', 'create')->name('create');
    Route::post('contacts', 'store')->name('store');
    Route::get('contacts/{contact}/edit', 'edit')->name('edit');
    Route::put('contacts/{contact}', 'update')->name('update');
    Route::delete('contacts/{contact}', 'destroy')->name('destroy');
    Route::put('contacts/{contact}/restore', 'restore')->name('restore');
});

Route::controller(OrganizationsController::class)->name('organizations.')->prefix('organizations/')->middleware(['auth'])->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{organization}/edit', 'edit')->name('edit');
    Route::put('/{organization}', 'update')->name('update');
    Route::delete('/{organization}', 'destroy')->name('destroy');
    Route::put('/{organization}/restore', 'restore')->name('restore');
});


Route::get('/img/{path}', function (Request $request, $path) {
    $filesystem = Storage::disk();
    $server = ServerFactory::create([
        'response' => new SymfonyResponseFactory($request),
        'source' => $filesystem->getDriver(),
        'cache' => $filesystem->getDriver(),
        'cache_path_prefix' => '.glide-cache',
    ]);

    return $server->getImageResponse($path, $request->query());
})->where('path', '.*')->name('image');
