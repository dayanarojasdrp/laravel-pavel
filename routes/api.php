<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\MensajeContactoController;
use App\Http\Controllers\MinisterioController;
use App\Http\Controllers\MisioneController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\PaginaInstitucionalController;
use App\Http\Controllers\RecursoController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public auth routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware('throttle:password-reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('throttle:password-reset');

/*
|--------------------------------------------------------------------------
| Public site routes
|--------------------------------------------------------------------------
*/

Route::get('/buscar', [BusquedaController::class, 'index']);

Route::apiResource('ministerios', MinisterioController::class)
    ->only(['index', 'show']);

Route::apiResource('noticias', NoticiaController::class)
    ->only(['index', 'show']);

Route::apiResource('eventos', EventoController::class)
    ->only(['index', 'show']);

Route::get('/ministerios/{id}/noticias', [NoticiaController::class, 'porMinisterio']);

Route::apiResource('misiones', MisioneController::class)
    ->only(['index', 'show']);

Route::apiResource('recursos', RecursoController::class)
    ->only(['index', 'show']);

Route::apiResource('paginas', PaginaInstitucionalController::class)
    ->only(['index', 'show']);

Route::post('/contactos', [MensajeContactoController::class, 'store'])
    ->middleware('throttle:contact');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    /*
    |--------------------------------------------------------------------------
    | Configured admin only
    |--------------------------------------------------------------------------
    |
    | Only the user whose email matches ADMIN_EMAIL can modify site content.
    |
    */

    Route::middleware('admin.owner')->group(function () {
        Route::apiResource('ministerios', MinisterioController::class)
            ->except(['index', 'show']);

        Route::apiResource('noticias', NoticiaController::class)
            ->except(['index', 'show']);

        Route::apiResource('eventos', EventoController::class)
            ->except(['index', 'show']);

        Route::apiResource('misiones', MisioneController::class)
            ->except(['index', 'show']);

        Route::apiResource('recursos', RecursoController::class)
            ->except(['index', 'show']);

        Route::apiResource('contactos', MensajeContactoController::class)
            ->except(['store']);

        Route::apiResource('paginas', PaginaInstitucionalController::class)
            ->except(['index', 'show']);

        Route::post('/uploads/imagenes', [UploadController::class, 'store']);
    });
});
