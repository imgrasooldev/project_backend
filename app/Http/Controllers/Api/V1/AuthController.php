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
use App\Services\Api\V1\OtpServiceForRegister;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;



class AuthController extends BaseController
{
    // public function signin(Request $request)
    // {
    //     $request->validate([
    //         'email_or_phone' => 'required',
    //         'password' => 'required'
    //     ]);

    // // 🔹 Determine if it's email or phone
    //     $login_type = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

    //     if (Auth::attempt([$login_type => $request->email_or_phone, 'password' => $request->password])) {
    //         $authUser = Auth::user();
    //         $success['token'] = $authUser->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken;
    //         $success['name'] = $authUser->name;

    //     // 🔹 Save Device Token
    //         if ($request->has('device_token')) {
    //             UserDeviceToken::updateOrCreate(
    //                 ['device_token' => $request->device_token],
    //                 [
    //                     'user_id' => $authUser->id,
    //                     'device_type' => $request->device_type ?? 'android',
    //                     'device_name' => $request->device_name ?? null,
    //                 ]
    //             );
    //         }

    //         return $this->sendResponse($success, 'User signed in');
    //     } else {
    //         return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
    //     }
    // }


    // public function signup(SignUpRequest $request, OtpServiceForRegister $otpService)
    // {
    //     $data = $request->only(['name', 'email', 'phone', 'password']);

    //     $user = User::create($data);

    //     // Send OTP for verification
    //     $otpService->sendOtp($user);

    //     $success['token'] = $user->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken;
    //     $success['user'] = new UserResource($user);

    // // Save Device Token if available
    //     if ($request->has('device_token')) {
    //         UserDeviceToken::updateOrCreate(
    //             ['device_token' => $request->device_token],
    //             [
    //                 'user_id' => $user->id,
    //                 'device_type' => $request->device_type ?? 'android',
    //                 'device_name' => $request->device_name ?? null,
    //             ]
    //         );
    //     }

    //     return $this->sendResponse($success, 'User created successfully.');
    // }


    public function signin(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required',
            'password' => 'required'
        ]);

        $login_type = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$login_type => $request->email_or_phone, 'password' => $request->password])) {
            $user = Auth::user();

            $token = $user->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken;

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

            return $this->sendResponse([
                'token' => $token,
                'user' => new UserResource($user)
            ], 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
        }
    }

    public function signup(SignUpRequest $request, OtpServiceForRegister $otpService)
    {
        $data = $request->only(['name', 'email', 'phone', 'password']);
        $user = User::create($data);

        // Send OTP for verification
        $otpService->sendOtp($user);

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

        return $this->sendResponse([
            'token' => $user->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken,
            'user' => new UserResource($user)
        ], 'User created successfully.');
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

    // AuthController.php
//     public function verifyOtp(Request $request, OtpServiceForRegister $otpService)
//     {
//         $request->validate([
//             'otp' => 'required|digits:6',
//         ]);

//     $user = $request->user(); // Authenticated user

//     if (!$user) {
//         return $this->sendError('Unauthenticated.', [], 401);
//     }

//     // Determine OTP type automatically
//     $type = null;
//     if ($user->email) {
//         $type = 'email';
//     } elseif ($user->phone) {
//         $type = 'phone';
//     } else {
//         return $this->sendError('No contact info available for OTP.');
//     }

//     if ($otpService->verifyOtp($user, $request->otp, $type)) {
//         return $this->sendResponse([], 'OTP verified successfully.');
//     } else {
//         return $this->sendError('Invalid or expired OTP.');
//     }
// }

public function verifyOtp(Request $request, OtpServiceForRegister $otpService)
{
    $request->validate([
        'otp' => 'required|digits:6',
    ]);

    $user = $request->user(); // Authenticated user

    if (!$user) {
        return $this->sendError('Unauthenticated.', [], 401);
    }

    // Determine OTP type automatically
    $type = null;
    if ($user->email) {
        $type = 'email';
    } elseif ($user->phone) {
        $type = 'phone';
    } else {
        return $this->sendError('No contact info available for OTP.');
    }

    if ($otpService->verifyOtp($user, $request->otp, $type)) {

        // 🔹 Generate token
        $token = $user->createToken('admin-token', ['create', 'read', 'update', 'delete'])->plainTextToken;

        // 🔹 Save device token if provided (optional)
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

        // 🔹 Return token and user data
        return $this->sendResponse([
            'token' => $token,
            'user' => new UserResource($user)
        ], 'OTP verified successfully.');
        
    } else {
        return $this->sendError('Invalid or expired OTP.', null, 400);
    }
}




public function resendOtp(Request $request, OtpServiceForRegister $otpService)
{
    $user = $request->user(); // Authenticated user

    if (!$user) {
        return $this->sendError('Unauthenticated.', [], 401);
    }

    // Determine OTP type automatically
    $type = null;
    if ($user->email) {
        $type = 'email';
    } elseif ($user->phone) {
        $type = 'phone';
    } else {
        return $this->sendError('No contact info available for OTP.');
    }

    // Send OTP
    $otpService->sendOtp($user, $type);

    return $this->sendResponse([], 'OTP resent successfully.');
}

// forgot reset password methods will be here
public function forgotPassword(Request $request)
{
    $request->validate(['email_or_phone' => 'required']);

    $user = User::where('email', $request->email_or_phone)
                ->orWhere('phone', $request->email_or_phone)
                ->first();

    if (!$user) {
        return $this->sendError('User not found.', [], 404);
    }

    $otp = rand(100000, 999999);

    // Store OTP (don't set token yet)
    DB::table('password_reset_tokens')->updateOrInsert(
        // Use either email or phone as keys. Keep it broad:
        [
            'email' => $user->email,
            'phone' => $user->phone
        ],
        [
            'otp' => $otp,
            'token' => null,
            'created_at' => Carbon::now(),
        ]
    );

    // Send OTP via email or SMS
    if ($user->email) {
        Mail::raw("Your password reset OTP is: {$otp}", function ($message) use ($user) {
            $message->to($user->email)->subject('Password Reset OTP');
        });
    }
    // TODO: send SMS if phone exists and you have SMS gateway

    return $this->sendResponse([], 'OTP sent successfully.');
}

public function verifyForgotOtp(Request $request)
{
    $request->validate([
        'email_or_phone' => 'required',
        'otp' => 'required|digits:6',
    ]);

    $user = User::where('email', $request->email_or_phone)
                ->orWhere('phone', $request->email_or_phone)
                ->first();

    if (!$user) {
        return $this->sendError('User not found.', [], 404);
    }

    $reset = DB::table('password_reset_tokens')
        ->where(function ($q) use ($user) {
            if ($user->email) $q->where('email', $user->email);
            if ($user->phone) $q->orWhere('phone', $user->phone);
        })
        ->where('otp', $request->otp)
        ->first();

    if (!$reset) {
        return $this->sendError('Invalid OTP.', [], 400);
    }

    // Expiry check (15 minutes)
    if (Carbon::parse($reset->created_at)->addMinutes(15)->isPast()) {
        return $this->sendError('OTP expired.', [], 400);
    }

    // Generate reset token, clear otp so OTP can't be reused
    $resetToken = Str::random(80);

    DB::table('password_reset_tokens')
        ->where('id', $reset->id)
        ->update([
            'token' => $resetToken,
            'otp' => null,
            'created_at' => Carbon::now(), // reset created_at timer if you want
        ]);

    return $this->sendResponse(['reset_token' => $resetToken], 'OTP verified. Use reset_token to change password.');
}


public function resetPassword(Request $request)
{
    $request->validate([
        'email_or_phone' => 'required',
        'password' => 'required|min:6|confirmed',
        'otp' => 'nullable|digits:6',
        'reset_token' => 'nullable|string',
    ]);

    $user = User::where('email', $request->email_or_phone)
                ->orWhere('phone', $request->email_or_phone)
                ->first();

    if (!$user) {
        return $this->sendError('User not found.', [], 404);
    }

    // Lookup reset row by email/phone
    $query = DB::table('password_reset_tokens')
        ->where(function ($q) use ($user) {
            if ($user->email) $q->where('email', $user->email);
            if ($user->phone) $q->orWhere('phone', $user->phone);
        });

    // Prefer reset_token if present
    if ($request->filled('reset_token')) {
        $reset = $query->where('token', $request->reset_token)->first();
    } else {
        $reset = $query->where('otp', $request->otp)->first();
    }

    if (!$reset) {
        return $this->sendError('Invalid reset token or OTP.', [], 400);
    }

    // Expiry check (token or otp should be recent)
    if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
        return $this->sendError('Reset token expired.', [], 400);
    }

    // Update password
    $user->password = Hash::make($request->password);
    $user->save();

    // Remove reset row to prevent reuse
    DB::table('password_reset_tokens')
        ->where('id', $reset->id)
        ->delete();

    return $this->sendResponse([], 'Password reset successfully.');
}

}
