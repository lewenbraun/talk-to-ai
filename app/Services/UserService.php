<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class UserService
{
    public function createTemporaryUser()
    {
        $user = User::create([
            'isTemporary' => true
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return $token;
    }
}
