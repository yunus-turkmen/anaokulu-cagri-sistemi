<?php

use App\Http\Controllers\Api\KioskCallController;
use Illuminate\Support\Facades\Route;

Route::post('/kiosk/activate', [KioskCallController::class, 'activate'])
    ->name('api.kiosk.activate');

Route::post('/kiosk/call', [KioskCallController::class, 'store'])
    ->name('api.kiosk.call');