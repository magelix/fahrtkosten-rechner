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

Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    // Check if user is authenticated
    if (!auth()->check()) {
        return redirect()->route('login')->withErrors(['email' => 'Sie müssen angemeldet sein, um Ihre E-Mail zu bestätigen.']);
    }
    
    $user = auth()->user();
    
    // Check if email is already verified
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('dashboard')->with('info', 'Ihre E-Mail-Adresse ist bereits bestätigt.');
    }
    
    // Check if the signature is valid (manual check to avoid 403)
    if (!$request->hasValidSignature()) {
        return redirect()->route('verification.notice')->withErrors(['email' => 'Dieser Bestätigungslink ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen an.']);
    }
    
    // Check if verification link is for this user
    if (!hash_equals((string) $request->route('id'), (string) $user->getKey())) {
        return redirect()->route('verification.notice')->withErrors(['email' => 'Dieser Bestätigungslink ist ungültig.']);
    }

    if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        return redirect()->route('verification.notice')->withErrors(['email' => 'Dieser Bestätigungslink ist ungültig.']);
    }
    
    // Mark email as verified
    $user->markEmailAsVerified();
    
    return redirect()->route('dashboard')->with('success', 'E-Mail-Adresse erfolgreich bestätigt!');
})->middleware('auth')->name('verification.verify');

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

Route::get('/reset-password/{token}', function (Request $request, string $token) {
    $email = $request->get('email');
    if (!$email) {
        return redirect()->route('password.request')
            ->withErrors(['email' => 'Dieser Reset-Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen an.']);
    }
    
    // Check if the token is valid before showing the form
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        return redirect()->route('password.request')
            ->withErrors(['email' => 'Wir konnten keinen Benutzer mit dieser E-Mail-Adresse finden.']);
    }
    
    // Check if token exists in password_reset_tokens table
    $tokenRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
        ->where('email', $email)
        ->first();
    
    if (!$tokenRecord || !\Illuminate\Support\Facades\Hash::check($token, $tokenRecord->token)) {
        return redirect()->route('password.request')
            ->withErrors(['email' => 'Dieser Reset-Link ist ungültig oder wurde bereits verwendet. Bitte fordern Sie einen neuen an.']);
    }
    
    // Check if token is expired (default: 60 minutes)
    $expiry = now()->subMinutes(config('auth.passwords.users.expire', 60));
    if ($tokenRecord->created_at < $expiry) {
        return redirect()->route('password.request')
            ->withErrors(['email' => 'Dieser Reset-Link ist abgelaufen. Bitte fordern Sie einen neuen an.']);
    }
    
    return view('auth.reset-password', ['token' => $token, 'email' => $email]);
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

    if ($status === Password::PASSWORD_RESET) {
        return redirect()->route('login')->with('success', 'Ihr Passwort wurde erfolgreich zurückgesetzt!');
    }

    $errorMessage = match($status) {
        Password::INVALID_TOKEN => 'Dieser Reset-Link ist ungültig oder wurde bereits verwendet. Bitte fordern Sie einen neuen an.',
        Password::INVALID_USER => 'Wir konnten keinen Benutzer mit dieser E-Mail-Adresse finden.',
        default => 'Es gab ein Problem beim Zurücksetzen Ihres Passworts. Bitte versuchen Sie es erneut.'
    };

    return back()->withErrors(['email' => $errorMessage]);
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
