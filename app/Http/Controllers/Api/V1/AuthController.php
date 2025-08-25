<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\V1\SignUpRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash; // Add this import
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    public function signin(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['token'] = $authUser->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken;
            $success['name'] = $authUser->name;
            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function signup(SignUpRequest $request)
    {
        $user = User::create($request->all());
        // To send custom welcome email for testing queues with redis
        // dispatch(new \App\Jobs\EmailJobs\Auth\SendRegisterMailJob($request->email));
        // event(new Registered($user));
        $success['token'] = $user->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken;
        $success['user'] = new UserResource($user);
        return $this->sendResponse($success, 'User created successfully.');
    }

     public function signout(Request $request)
{
    $user = $request->user();

    if ($user && $user->currentAccessToken()) {
        $user->currentAccessToken()->delete();
    }

    return response()->json([
        'success' => true,
        'message' => 'User logged out successfully.'
    ]);
}

/* public function googleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'google_id' => 'required|string',
            'avatar' => 'nullable|url',
        ]);

        try {
            // Find user by google_id or email
            $user = User::where('google_id', $request->google_id)
                        ->orWhere('email', $request->email)
                        ->first();

            if ($user) {
                // Update existing user with Google data
                $user->update([
                    'google_id' => $request->google_id,
                    'avatar' => $request->avatar,
                    'last_login_at' => now(),
                ]);
            } else {
                // Create new user for Google authentication
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'avatar' => $request->avatar,
                    'password' => Hash::make(Str::random(24)), // Random password for Google users
                    'user_type_id' => 1, // Default user type, adjust as needed
                    'last_login_at' => now(),
                ]);
            }

            // Generate token
            $token = $user->createToken('google-token', ['create', 'read', 'update', 'delete'])->plainTextToken;

            $success = [
                'token' => $token,
                'user' => new UserResource($user),
            ];

            return $this->sendResponse($success, 'Google login successful.');

        } catch (\Exception $e) {
            return $this->sendError('Google login failed.', ['error' => $e->getMessage()], 500);
        }
    } */

        public function googleLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'name' => 'required|string',
        'google_id' => 'required|string',
        'avatar' => 'nullable|url',
    ]);

    try {
        // Find user by google_id or email
        $user = User::where('google_id', $request->google_id)
                    ->orWhere('email', $request->email)
                    ->first();

        if ($user) {
            // Update existing user with Google data
            $user->update([
                'google_id' => $request->google_id,
                'avatar' => $request->avatar,
                'last_login_at' => now(),
                'name' => $request->name, // Update name in case it changed
            ]);
        } else {
            // Create new user for Google authentication with proper defaults
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'google_id' => $request->google_id,
                'avatar' => $request->avatar,
                'password' => Hash::make(Str::random(24)), // Random password for Google users
                'user_type_id' => 1, // Default user type (required field)
                'phone' => null, // Explicitly set to null
                'city_id' => null, // Explicitly set to null
                'bio' => null, // Explicitly set to null
                'last_login_at' => now(),
            ]);
        }

        // Generate token
        $token = $user->createToken('google-token', ['create', 'read', 'update', 'delete'])->plainTextToken;

        $success = [
            'token' => $token,
            'user' => new UserResource($user),
        ];

        return $this->sendResponse($success, 'Google login successful.');

    } catch (\Exception $e) {
        return $this->sendError('Google login failed.', ['error' => $e->getMessage()], 500);
    }
}

}
