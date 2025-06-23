<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WorkplaceController;

Route::get('/', [TripController::class, 'index'])->name('home');
Route::resource('trips', TripController::class);
Route::resource('workplaces', WorkplaceController::class);
Route::get('/api/workplaces/{workplace}', [TripController::class, 'getWorkplaceData'])->name('api.workplace.data');
