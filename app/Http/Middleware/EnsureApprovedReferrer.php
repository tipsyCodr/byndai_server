<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedReferrer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('referrer.login');
        }

        $referrer = $user->referrer;

        if (! $referrer || ! $referrer->isApproved()) {
            Auth::logout();
            return redirect()->route('referrer.login')->withErrors([
                'email' => 'Your referrer registration is pending approval or has been disabled by Admin.',
            ]);
        }

        return $next($request);
    }
}
