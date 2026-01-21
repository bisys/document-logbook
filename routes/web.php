<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('dashboard');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        });
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
