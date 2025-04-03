<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Contracts\Auth\Authenticatable;
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

            if ($personalAccessToken && ($personalAccessToken->expires_at === null || now()->lessThan($personalAccessToken->expires_at))) {
                $tokenable = $personalAccessToken->tokenable;
                if ($tokenable instanceof Authenticatable) {
                    Auth::setUser($tokenable);
                    \Log::info('OptionalAuth: User set');
                } else {
                    \Log::warning('OptionalAuth: Tokenable is not authenticatable');
                }
            } elseif ($personalAccessToken) {
                \Log::warning('OptionalAuth: Token is invalid or expired');
            } else {
                \Log::warning('OptionalAuth: Token not found');
            }
        }

        return $next($request);
    }
}
