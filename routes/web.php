<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffRecordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Root route - redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes for guest users (not logged in)
Route::middleware('guest')->group(function () {
    // Show login page
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    // Process login form
    Route::post('/login', [AuthController::class, 'login']);

    // Show registration page
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

    // Process registration form
    Route::post('/register', [AuthController::class, 'register']);
});

// Routes for authenticated users (logged in)
Route::middleware('auth')->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard - accessible to all logged in users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin only routes - manage users
    Route::middleware('role:admin')->group(function () {
        // View all users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');

        // Create new user
        Route::post('/users', [UserController::class, 'store'])->name('users.store');

        // Update existing user
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

        // Delete user
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Admin only routes - manage staff records
    Route::middleware('role:admin')->group(function () {
        // View all staff records
        Route::get('/staff', [StaffRecordController::class, 'index'])->name('staff.index');

        // Create new staff record
        Route::post('/staff', [StaffRecordController::class, 'store'])->name('staff.store');

        // Update staff record
        Route::put('/staff/{staffRecord}', [StaffRecordController::class, 'update'])->name('staff.update');

        // Delete staff record
        Route::delete('/staff/{staffRecord}', [StaffRecordController::class, 'destroy'])->name('staff.destroy');
    });

    // All users can manage their own profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
