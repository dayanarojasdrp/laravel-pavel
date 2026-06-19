<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrations-secret-2026', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    $seedOutput = \Illuminate\Support\Facades\Artisan::output();

    return '<pre>' . $migrateOutput . "\n\n" . $seedOutput . '</pre>';
});
