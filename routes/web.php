<?php

use App\Http\Controllers\ProfileController;
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
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);

    Route::get('tenants/change/{tenantID}', [\App\Http\Controllers\TenantController::class, 'changeTenant'])
         ->name('tenants.change');

         
    Route::resource('users', \App\Http\Controllers\UserController::class)
        ->only('index', 'store')
        ->middleware('can:manage_users');

    Route::get('invitations/{token}', [\App\Http\Controllers\UserController::class, 'acceptInvitation'])
    ->name('invitations.accept');    
});

require __DIR__.'/auth.php';
