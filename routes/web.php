<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\UserController;

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['role:admin'])->prefix('/admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        });

        Route::resource('/department', DepartmentController::class);
        Route::resource('/position', PositionController::class);
        Route::resource('/role', RoleController::class);
        Route::get('/role/{role}/permission', [RoleController::class, 'permissions']);
        Route::post('/role/{role}/permission', [RoleController::class, 'syncPermissions']);
        Route::resource('/permission', PermissionController::class);
        Route::resource('/approval', ApprovalController::class);
        Route::resource('/user', UserController::class);
    });

    Route::middleware(['role:accounting'])->group(function () {
        Route::get('/accounting/dashboard', function () {
            return view('accounting.dashboard');
        });
    });

    Route::middleware(['role:user'])->group(function () {
        Route::get('/user/dashboard', function () {
            return view('user.dashboard');
        });
    });
});
