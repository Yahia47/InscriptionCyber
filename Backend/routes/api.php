<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\RegistrationController;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Workshops publics (lecture seule)
Route::get('/workshops', [WorkshopController::class, 'index']);
Route::get('/workshops/{id}', [WorkshopController::class, 'show']);

// Routes protégées (authentification requise)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Inscriptions
    Route::post('/workshops/{id}/register', [RegistrationController::class, 'register']);
    Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations']);
    Route::delete('/workshops/{id}/cancel', [RegistrationController::class, 'cancel']);
});

// Routes Admin (authentification + rôle admin requis)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Gestion des workshops
    Route::post('/workshops', [WorkshopController::class, 'store']);
    Route::put('/workshops/{id}', [WorkshopController::class, 'update']);
    Route::delete('/workshops/{id}', [WorkshopController::class, 'destroy']);

    // Gestion des inscriptions
    Route::get('/workshops/{id}/registrations', [WorkshopController::class, 'registrations']);
    Route::post('/workshops/{id}/send-message', [WorkshopController::class, 'sendMessage']);
});
