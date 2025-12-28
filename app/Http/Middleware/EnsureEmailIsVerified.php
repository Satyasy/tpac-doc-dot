<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     * Redirect unverified users to OTP verification page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->isVerified()) {
            // Allow access to verify-otp and resend-otp routes
            if ($request->routeIs('verify.otp', 'resend.otp', 'logout')) {
                return $next($request);
            }
            
            return redirect()->route('verify.otp');
        }

        return $next($request);
    }
}
