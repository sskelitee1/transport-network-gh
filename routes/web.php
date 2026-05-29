<?php

use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\LineController;
use App\Http\Controllers\Admin\StationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('lines', LineController::class)->only(['index']);
    Route::resource('stations', StationController::class)->only(['index']);
    Route::resource('vehicles', VehicleController::class)->only(['index']);
    Route::resource('drivers', DriverController::class)->only(['index']);

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('lines', LineController::class)->except(['index']);
        Route::resource('stations', StationController::class)->except(['index']);
        Route::resource('vehicles', VehicleController::class)->except(['index']);
        Route::resource('drivers', DriverController::class)->except(['index']);
    });
});
