<?php

use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Routes عامة
Route::get('workshops', [WorkshopController::class, 'index']);
Route::get('workshops/{id}', [WorkshopController::class, 'show']);

// Routes محمية (للطلبة المسجلين)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('workshops/{id}/register', [RegistrationController::class, 'register']);
    Route::get('my-registrations', [RegistrationController::class, 'myRegistrations']);
});

// Routes للـ Admin فقط
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::resource('workshops', WorkshopController::class)->except(['index', 'show']);
    Route::get('workshops/{id}/registrations', [WorkshopController::class, 'registrations']);
    Route::post('workshops/{id}/send-message', [WorkshopController::class, 'sendMessage']);
});
