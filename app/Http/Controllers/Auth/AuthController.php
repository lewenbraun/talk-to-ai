<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): Response|ResponseFactory
    {
        $user = User::create([
            'name' => $request['name'],
            'nickname' => $request['nickname'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(LoginRequest $request): Response|ResponseFactory
    {
        $credentials = $request->validate([
            'email' => 'required|email|string|exists:users,email',
            'password' => [
                'required',
            ],
            'remember' => 'boolean',
        ]);
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (! Auth::attempt($credentials, $remember)) {
            return response([
                'error' => 'The Provided credentials are not correct',
            ], 422);
        }

        $user = Auth::user();

        $token = $user->createToken('main')->plainTextToken;

        return response([
            'token' => $token,
        ]);
    }

    public function logout(): Response|ResponseFactory
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response([
            'success' => true,
        ]);
    }

    public function createTemporaryUser()
    {
        $user = User::create([
            'isTemporary' => true
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return ['token' => $token];
    }
}
