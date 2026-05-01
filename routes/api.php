<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\MinisterioController;
use App\Http\Controllers\MisioneController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\RecursoController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin,pastor')->group(function () {
        Route::apiResource('ministerios', MinisterioController::class)->except(['index', 'show']);
        Route::apiResource('noticias', NoticiaController::class)->except(['index', 'show']);
        Route::apiResource('eventos', EventoController::class)->except(['index', 'show']);
        Route::apiResource('misiones', MisioneController::class)->except(['index', 'show']);
        Route::apiResource('recursos', RecursoController::class)->except(['index', 'show']);
    });
});

Route::apiResource('ministerios', MinisterioController::class)->only(['index', 'show']);
Route::apiResource('noticias', NoticiaController::class)->only(['index', 'show']);
Route::apiResource('eventos', EventoController::class)->only(['index', 'show']);
Route::get('/ministerios/{id}/noticias', [NoticiaController::class, 'porMinisterio']);
Route::apiResource('misiones', MisioneController::class)->only(['index', 'show']);
Route::apiResource('recursos', RecursoController::class)->only(['index', 'show']);
