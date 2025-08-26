<?php

namespace App\Services\Api\V1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthService
{
    public function handleGoogleLogin($data)
    {
        $user = User::where('google_id', $data['google_id'])
                    ->orWhere('email', $data['email'])
                    ->first();

        if ($user) {
            $user->update([
                'google_id' => $data['google_id'],
                'avatar' => $data['avatar'] ?? null,
                'last_login_at' => now(),
                'name' => $data['name'],
            ]);
        } else {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'google_id' => $data['google_id'],
                'avatar' => $data['avatar'] ?? null,
                'password' => Hash::make(Str::random(24)),
                'user_type_id' => 1,
                'phone' => null,
                'city_id' => null,
                'bio' => null,
                'last_login_at' => now(),
            ]);
        }

        return $user;
    }
}