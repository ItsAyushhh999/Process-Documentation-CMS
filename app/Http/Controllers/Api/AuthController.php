<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\User;

class AuthController extends Controller
{
    use ApiTrait;

    public function generateToken()
    {
        $user = User::where('id', 5)->first();

        if (!$user) {
            return $this->failure('User not found');
        }

        try {
            $token = $user->createToken('TDMS_LOGIN_TOKEN')->plainTextToken;
        } catch (\Exception $e) {
            return $this->failure('Failed to create token');
        }

        return $this->success('Token created successfully', $token);
    }
}
