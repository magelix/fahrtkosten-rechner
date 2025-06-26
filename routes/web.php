<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WorkplaceController;
use App\Http\Controllers\AuthController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard route (requires authentication)
Route::get('/dashboard', function () {
    $recentTrips = \App\Models\Trip::with('workplace')
        ->where('user_id', auth()->id())
        ->orderBy('departure_date', 'desc')
        ->limit(5)
        ->get();
    return view('dashboard', compact('recentTrips'));
})->middleware('auth')->name('dashboard');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('home');
    Route::resource('trips', TripController::class);
    Route::resource('workplaces', WorkplaceController::class);
    Route::post('/workplaces/{workplace}/set-default', [WorkplaceController::class, 'setDefault'])->name('workplaces.setDefault');
    Route::get('/api/workplaces/{workplace}', [TripController::class, 'getWorkplaceData'])->name('api.workplace.data');
});

// Redirect unauthenticated users to login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('welcome');
