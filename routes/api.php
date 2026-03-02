<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MisioneController;
use App\Http\Controllers\RecursoController;
use App\Http\Controllers\MinisterioController;
use App\Http\Controllers\NoticiaController;

Route::apiResource('ministerios', MinisterioController::class);
Route::apiResource('noticias', NoticiaController::class);
Route::get('/ministerios/{id}/noticias', [NoticiaController::class, 'porMinisterio']);
Route::apiResource('misiones', MisioneController::class);
Route::apiResource('recursos', RecursoController::class);