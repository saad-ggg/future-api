<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardUserController;
use App\Http\Controllers\Web\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Authentication Routes
 */
Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('admin/login', [AuthController::class, 'login'])->name('admin.login.submit');


/**
 * Protected Admin Dashboard Routes
 */
Route::group([
    'prefix' => 'admin', 
    'as' => 'admin.', 
    'middleware' => ['auth', 'admin']
], function() {
    
    /**
     * Logout Route
     */
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    /**
     * Main Dashboard Home
     */
    Route::get('/', function () {
        return view('admin.users.index', [
            'users' => \App\Models\User::latest()->paginate(10)
        ]);
    })->name('dashboard');

    /**
     * Users Management Resource (CRUD)
     */
    Route::resource('users', DashboardUserController::class);

    /**
     * User Status Actions
     */
    Route::post('users/{id}/ban', [DashboardUserController::class, 'ban'])->name('users.ban');
    Route::post('users/{id}/unban', [DashboardUserController::class, 'unban'])->name('users.unban');
});