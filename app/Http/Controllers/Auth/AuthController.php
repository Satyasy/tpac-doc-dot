<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is verified
            if (!$user->isVerified()) {
                // Generate and send OTP
                $otp = $user->generateOtp();
                Mail::to($user->email)->send(new OtpMail($otp, $user->name));

                return redirect()->route('verify.otp');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // Generate and send OTP
        $otp = $user->generateOtp();
        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        Auth::login($user);

        return redirect()->route('verify.otp');
    }

    public function showVerifyOtp()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isVerified()) {
            return redirect('/');
        }

        $resendStatus = $user->canResendOtp();

        return Inertia::render('Auth/VerifyOtp', [
            'email' => $user->email,
            'canResend' => $resendStatus['can_resend'],
            'waitSeconds' => $resendStatus['wait_seconds'],
            'remainingToday' => $resendStatus['remaining_today'],
            'nextCooldown' => $user->getNextCooldown(),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->verifyOtp($request->otp)) {
            return redirect()->intended('/')->with('success', 'Email berhasil diverifikasi!');
        }

        return back()->withErrors([
            'otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.',
        ]);
    }

    public function resendOtp()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isVerified()) {
            return redirect('/');
        }

        $resendStatus = $user->canResendOtp();

        if (!$resendStatus['can_resend']) {
            return back()->withErrors([
                'resend' => $resendStatus['reason'],
            ]);
        }

        $otp = $user->resendOtp();
        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return back()->with([
            'success' => 'Kode OTP baru telah dikirim ke email Anda.',
            'nextCooldown' => $user->getNextCooldown(),
            'remainingToday' => 5 - $user->otp_resend_count,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showProfile()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $user->load('profile');

        // Get statistics
        $chatSessionsCount = $user->chatSessions()->count();
        $totalMessages = \App\Models\ChatMessage::whereHas('session', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        return Inertia::render('Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ],
            'profile' => $user->profile ? [
                'gender' => $user->profile->gender,
                'photo_profile' => $user->profile->photo_profile,
                'birth_date' => $user->profile->birth_date?->format('Y-m-d'),
                'height' => $user->profile->height,
                'weight' => $user->profile->weight,
                'bmi' => $user->profile->bmi,
                'bmi_category' => $user->profile->bmi_category,
                'age' => $user->profile->age,
            ] : null,
            'stats' => [
                'chat_sessions' => $chatSessionsCount,
                'total_messages' => $totalMessages,
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'height' => ['nullable', 'integer', 'min:50', 'max:250'],
            'weight' => ['nullable', 'integer', 'min:20', 'max:300'],
        ]);

        // Update user basic info
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        // Update or create profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'height' => $validated['height'],
                'weight' => $validated['weight'],
            ]
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $path = $request->file('photo')->store('profile-photos', 'public');

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['photo_profile' => $path]
        );

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function showForgotPassword()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar.',
            ]);
        }

        // Delete any existing token for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Generate token
        $token = Str::random(64);

        // Store token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Send email with proper Mailable
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        Mail::to($user->email)->send(new ResetPasswordMail($resetUrl, $user->name));

        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }

    public function showResetPassword(Request $request, $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Find the reset token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors([
                'email' => 'Token reset password tidak valid.',
            ]);
        }

        // Check if token matches
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors([
                'email' => 'Token reset password tidak valid.',
            ]);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors([
                'email' => 'Token reset password sudah kadaluarsa.',
            ]);
        }

        // Update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'User tidak ditemukan.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}
