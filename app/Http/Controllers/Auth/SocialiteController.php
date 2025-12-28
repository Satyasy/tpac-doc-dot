<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
   /**
    * Redirect to Google OAuth
    */
   public function redirectToGoogle()
   {
      return Socialite::driver('google')->redirect();
   }

   /**
    * Handle Google OAuth callback
    */
   public function handleGoogleCallback()
   {
      try {
         $googleUser = Socialite::driver('google')->user();
      } catch (\Exception $e) {
         return redirect()->route('login')->withErrors([
            'google' => 'Gagal login dengan Google. Silakan coba lagi.',
         ]);
      }

      // Check if user already exists with this email
      $user = User::where('email', $googleUser->getEmail())->first();

      if ($user) {
         // User exists - update google_id if not set
         if (!$user->google_id) {
            $user->update([
               'google_id' => $googleUser->getId(),
            ]);
         }

         // Check if the account was created with password (non-OAuth)
         // If user tries to login with Google but account exists with password
         if ($user->google_id !== $googleUser->getId()) {
            return redirect()->route('login')->withErrors([
               'google' => 'Email ini sudah terdaftar dengan metode login lain.',
            ]);
         }

         // Login user
         Auth::login($user, true);

         // User is already verified via Google
         if (!$user->isVerified()) {
            $user->update([
               'email_verified_at' => now(),
            ]);
         }

         return redirect()->intended('/');
      }

      // Create new user
      $user = User::create([
         'name' => $googleUser->getName(),
         'email' => $googleUser->getEmail(),
         'google_id' => $googleUser->getId(),
         'password' => Hash::make(Str::random(24)), // Random password for OAuth users
         'role' => 'user',
         'email_verified_at' => now(), // Google emails are verified
      ]);

      // Create user profile with avatar from Google
      $user->profile()->create([
         'photo_profile' => null, // Could store Google avatar URL if needed
      ]);

      Auth::login($user, true);

      return redirect()->intended('/');
   }
}
