<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WorkplaceController;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard')->with('success', 'E-Mail-Adresse erfolgreich bestätigt!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Bestätigungs-E-Mail wurde erneut gesendet!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Dashboard route (requires authentication and email verification)
Route::get('/dashboard', function () {
    $recentTrips = \App\Models\Trip::with('workplace')
        ->where('user_id', auth()->id())
        ->orderBy('departure_date', 'desc')
        ->limit(5)
        ->get();
    return view('dashboard', compact('recentTrips'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Protected routes (require authentication and email verification)
Route::middleware(['auth', 'verified'])->group(function () {
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
