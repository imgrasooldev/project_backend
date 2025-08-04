<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    public function getAuthenticatedUser()
    {
        return Auth::user(); // Automatically gets user from Bearer Token (Sanctum)
    }
}
