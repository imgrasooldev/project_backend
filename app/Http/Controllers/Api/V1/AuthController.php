<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\GoogleLoginRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\V1\SignUpRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash; // Add this import
use Illuminate\Support\Str;
use App\Models\UserDeviceToken;

use App\Services\Api\V1\GoogleAuthService;

class AuthController extends BaseController
{
    public function signin(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required',
            'password' => 'required'
        ]);

    // 🔹 Determine if it's email or phone
        $login_type = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$login_type => $request->email_or_phone, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['token'] = $authUser->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken;
            $success['name'] = $authUser->name;

        // 🔹 Save Device Token
            if ($request->has('device_token')) {
                UserDeviceToken::updateOrCreate(
                    ['device_token' => $request->device_token],
                    [
                        'user_id' => $authUser->id,
                        'device_type' => $request->device_type ?? 'android',
                        'device_name' => $request->device_name ?? null,
                    ]
                );
            }

            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
        }
    }


    public function signup(SignUpRequest $request)
    {
        $data = $request->only(['name', 'email', 'phone', 'password']);

        $user = User::create($data);

        $success['token'] = $user->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken;
        $success['user'] = new UserResource($user);

    // Save Device Token if available
        if ($request->has('device_token')) {
            UserDeviceToken::updateOrCreate(
                ['device_token' => $request->device_token],
                [
                    'user_id' => $user->id,
                    'device_type' => $request->device_type ?? 'android',
                    'device_name' => $request->device_name ?? null,
                ]
            );
        }

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


    public function googleLogin(GoogleLoginRequest $request, GoogleAuthService $googleAuthService)
    {
        try {
            $user = $googleAuthService->handleGoogleLogin($request->validated());

            $token = $user->createToken('google-token', ['create', 'read', 'update', 'delete'])->plainTextToken;

            $success = [
                'token' => $token,
                'user' => new UserResource($user),
            ];

            if ($request->has('device_token')) {
                UserDeviceToken::updateOrCreate(
                    ['device_token' => $request->device_token],
                    [
                        'user_id' => $user->id,
                        'device_type' => $request->device_type ?? 'android',
                        'device_name' => $request->device_name ?? null,
                    ]
                );
            }



            return $this->sendResponse($success, 'Google login successful.');
        } catch (\Exception $e) {
            return $this->sendError('Google login failed.', ['error' => $e->getMessage()], 500);
        }
    }

}
