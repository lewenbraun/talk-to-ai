<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user || !$user->is_temporary) {
            return response()->json([
                'error' => __('errors.no_temporary_user'),
            ], 403);
        }

        $user->update([
            'email' => $request['email'],
            'password' => bcrypt((string) $request['password']),
            'is_temporary' => false,
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->toArray())) {
            return response()->json([
                'error' => __('errors.invalid_credentials'),
            ], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => __('errors.authentication_failed'),
            ], 401);
        }

        $token = $user->createToken('main')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return response()->json();
    }

    public function createTemporaryUser(): JsonResponse
    {
        $user = User::create([
            'is_temporary' => true,
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
