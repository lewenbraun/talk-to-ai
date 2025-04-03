<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class OptionalSanctumAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tokenValue = $request->bearerToken();

        if ($tokenValue) {
            $personalAccessToken = PersonalAccessToken::findToken($tokenValue);

            if ($personalAccessToken && ($personalAccessToken->expires_at === null || $personalAccessToken->isValid)) {
                Auth::setUser($personalAccessToken->tokenable);
                \Log::info('OptionalAuth: User set');
            } elseif ($personalAccessToken) {
                \Log::warning('OptionalAuth: Token is invalid');
            } else {
                \Log::warning('OptionalAuth: Token not found');
            }
        }

        return $next($request);
    }
}
