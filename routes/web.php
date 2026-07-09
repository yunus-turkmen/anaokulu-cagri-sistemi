<?php

use App\Http\Controllers\ClassScreenController;
use App\Http\Controllers\KioskScreenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/kiosk');
});

Route::get('/kiosk', [KioskScreenController::class, 'index'])->name('kiosk.index');
Route::get('/class-screen/{schoolClass}', [ClassScreenController::class, 'show'])->name('class-screen.show');
Route::post('/pickup-calls/{pickupCall}/complete', [ClassScreenController::class, 'complete'])->name('pickup-calls.complete');
