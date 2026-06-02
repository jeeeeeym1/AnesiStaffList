<?php

use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffRecordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes — all roles can log in freely
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard — controller handles role-based content/redirect
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',           [UserController::class, 'index'])->name('users.index');
        Route::post('/users',          [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',    [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Admin + Manager
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/staff',                  [StaffRecordController::class, 'index'])->name('staff.index');
        Route::post('/staff',                 [StaffRecordController::class, 'store'])->name('staff.store');
        Route::put('/staff/{staffRecord}',    [StaffRecordController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staffRecord}', [StaffRecordController::class, 'destroy'])->name('staff.destroy');

        Route::get('/schedules',               [ScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/schedules',              [ScheduleController::class, 'store'])->name('schedules.store');
        Route::put('/schedules/{schedule}',    [ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    // All roles — own profile
    Route::get('/profile',  [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Staff + manager + admin — view own schedule
    Route::get('/my-schedule', [ScheduleController::class, 'mySchedule'])->name('schedules.mine');
});
