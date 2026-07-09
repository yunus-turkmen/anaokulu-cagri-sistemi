<?php

use App\Http\Controllers\Api\KioskCallController;
use Illuminate\Support\Facades\Route;

Route::post('/kiosk/call', [KioskCallController::class, 'store']);
