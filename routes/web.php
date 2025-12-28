<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\HealthDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Redirect old features route to consultation
Route::get('/features', function () {
    return redirect()->route('consultation');
});

// Static Pages
Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:100',
        'subject' => 'required|string|max:50',
        'message' => 'required|string|max:2000',
    ]);

    // Here you could send an email or save to database
    // For now, just redirect with success
    return back()->with('success', 'Pesan berhasil dikirim!');
});

Route::get('/privacy-policy', function () {
    return Inertia::render('PrivacyPolicy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return Inertia::render('TermsOfService');
})->name('terms-of-service');

// Article Routes
Route::get('/article', [ArticleController::class, 'index'])->name('article');
Route::get('/article/{slug}', [ArticleController::class, 'show'])->name('article.show');

// Drug Catalog Routes
Route::get('/drug-catalog', [DrugController::class, 'index'])->name('drug-catalog');
Route::get('/drug-catalog/{drug}', [DrugController::class, 'show'])->name('drug-catalog.show');

// Consultation - accessible by everyone, but submit requires auth
Route::get('/consultation', [ChatController::class, 'index'])->name('consultation');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Google OAuth Routes
    Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

    // Forgot Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // OTP Verification (doesn't require verified middleware)
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify.otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend.otp');
});

// Routes that require verified email
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/photo', [AuthController::class, 'updateProfilePhoto'])->name('profile.photo');

    // Health Dashboard
    Route::get('/health-dashboard', [HealthDashboardController::class, 'index'])->name('health-dashboard');
    Route::post('/health-dashboard/physical', [HealthDashboardController::class, 'storePhysical']);
    Route::post('/health-dashboard/mental', [HealthDashboardController::class, 'storeMental']);

    // Chat History
    Route::get('/chat-history', [ChatController::class, 'history'])->name('chat.history');

    // Consultation Chat (requires auth + verified)
    Route::post('/consultation/session', [ChatController::class, 'createSession']);
    Route::get('/consultation/session/{session}/messages', [ChatController::class, 'getMessages']);
    Route::post('/consultation/session/{session}/message', [ChatController::class, 'sendMessage']);
    Route::delete('/consultation/session/{session}', [ChatController::class, 'deleteSession']);

    // Doctor-Patient System
    Route::prefix('doctor-patient')->group(function () {
        // Patient endpoints
        Route::get('/doctors', [\App\Http\Controllers\DoctorPatientController::class, 'getDoctors']);
        Route::post('/request', [\App\Http\Controllers\DoctorPatientController::class, 'sendRequest']);
        Route::get('/my-doctors', [\App\Http\Controllers\DoctorPatientController::class, 'getMyDoctors']);
        Route::delete('/cancel/{doctorPatient}', [\App\Http\Controllers\DoctorPatientController::class, 'cancelRequest']);
        Route::delete('/disconnect/{doctorPatient}', [\App\Http\Controllers\DoctorPatientController::class, 'disconnect']);

        // Doctor endpoints
        Route::get('/pending-requests', [\App\Http\Controllers\DoctorPatientController::class, 'getPendingRequests']);
        Route::post('/accept/{doctorPatient}', [\App\Http\Controllers\DoctorPatientController::class, 'acceptRequest']);
        Route::post('/reject/{doctorPatient}', [\App\Http\Controllers\DoctorPatientController::class, 'rejectRequest']);
        Route::get('/my-patients', [\App\Http\Controllers\DoctorPatientController::class, 'getMyPatients']);
        Route::get('/patient/{doctorPatient}', [\App\Http\Controllers\DoctorPatientController::class, 'getPatientDetail']);
        Route::post('/patient/{doctorPatient}/mark-read', [\App\Http\Controllers\DoctorPatientController::class, 'markAlertsRead']);
        Route::delete('/patient/{doctorPatient}', [\App\Http\Controllers\DoctorPatientController::class, 'removePatient']);
    });

    // Doctor Pages (for doctors only)
    Route::get('/doctor/patients', function () {
        $user = auth()->user();
        if (!$user->hasRole('doctor')) {
            abort(403);
        }
        return Inertia::render('Doctor/Patients');
    })->name('doctor.patients');
});
