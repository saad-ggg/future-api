<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Dashboard Routes
 */
Route::group([
    'prefix' => 'admin', 
    'as' => 'admin.', 
    'middleware' => ['auth', 'admin']
], function() {

    // Main dashboard view
    Route::get('/', function () {
        return view('admin.index');
    })->name('dashboard');

    /*
      Users Management CRUD
     */
    Route::resource('users', DashboardUserController::class);

    /**
     * Extra User Status Actions
     * Dedicated routes for banning and unbanning users from the web interface
     */
    Route::post('users/{id}/ban', [DashboardUserController::class, 'ban'])->name('users.ban');
    Route::post('users/{id}/unban', [DashboardUserController::class, 'unban'])->name('users.unban');
});