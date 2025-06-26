<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WorkplaceController;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

// Password reset routes
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['success' => 'Reset-Link wurde an Ihre E-Mail-Adresse gesendet!'])
                : back()->withErrors(['email' => 'Wir konnten keinen Benutzer mit dieser E-Mail-Adresse finden.']);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ], [
        'password.confirmed' => 'Die Passwort-Bestätigung stimmt nicht überein.',
        'password.min' => 'Das Passwort muss mindestens :min Zeichen haben.',
        'email.required' => 'Die E-Mail-Adresse ist erforderlich.',
        'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('success', 'Ihr Passwort wurde erfolgreich zurückgesetzt!')
                : back()->withErrors(['email' => 'Es gab ein Problem beim Zurücksetzen Ihres Passworts.']);
})->middleware('guest')->name('password.update');

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
