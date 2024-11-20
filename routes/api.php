<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\EmployeManagerController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/', 'login');
    Route::post('logout', 'logout')->middleware('auth:api');
    Route::post('refresh', 'refresh')->middleware('auth:api');
    Route::get('user', 'getAuthenticatedUser')->middleware('auth:api');
});

Route::controller(CompanyController::class)
    ->prefix('company')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

Route::controller(EmployeManagerController::class)
    ->prefix('manager')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::put('/', 'edit');
        Route::delete('/{id}', 'destroy');
    });

Route::controller(EmployeController::class)
    ->prefix('employe')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::put('/', 'edit');
        Route::delete('/{id}', 'destroy');
    });
