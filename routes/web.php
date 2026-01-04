<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardUserController;
use App\Http\Controllers\Web\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Authentication Routes
 * These routes are public and accessible without logging in.
 */
Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [AuthController::class, 'logout'])->name('logout');


/**
 * Protected Admin Dashboard Routes
 * Secured by 'auth' (session) and 'admin' (role check) middlewares.
 */
Route::group([
    'prefix' => 'admin', 
    'as' => 'admin.', 
    'middleware' => ['auth', 'admin']
], function() {
    
    /**
     * Main Dashboard Home
     */
    Route::get('/', function () {
        return view('admin.index');
    })->name('dashboard');

    /**
     * Users Management Resource (CRUD)
     */
    Route::resource('users', DashboardUserController::class);

    /**
     * Extended User Status Actions
     * Handle account suspension and activation.
     */
    Route::post('users/{id}/ban', [DashboardUserController::class, 'ban'])->name('users.ban');
    Route::post('users/{id}/unban', [DashboardUserController::class, 'unban'])->name('users.unban');
});