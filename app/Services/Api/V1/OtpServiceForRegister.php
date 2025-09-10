<?php

namespace App\Services\Api\V1;

use App\Models\UserVerification;
use App\Mail\SendOtpEmailForRegister;
use Illuminate\Support\Facades\Mail;

class OtpServiceForRegister
{
    public function generateOtp($user, $type = 'email')
    {
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        // 🔹 Check if there's an existing unverified OTP for this user/type
        $verification = UserVerification::where('user_id', $user->id)
        ->where('type', $type)
        ->where('is_verified', false)
        ->first();

        if ($verification) {
            // Update existing record
            $verification->update([
                'otp' => $otp,
                'expires_at' => $expiresAt,
            ]);
        } else {
            // Create new record if none exists
            UserVerification::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'type' => $type,
                'expires_at' => $expiresAt
            ]);
        }

        return $otp;
    }

    public function sendEmailOtp($user, $otp)
    {
        Mail::to($user->email)->queue(new SendOtpEmailForRegister($user, $otp));
    }

    public function sendSmsOtp($user, $otp)
    {
        // Example using Twilio
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_NUMBER');
        $client = new \Twilio\Rest\Client($sid, $token);
        $client->messages->create($user->phone, [
            'from' => $from,
            'body' => "Your OTP is: $otp"
        ]);
    }

    public function sendOtp($user, $type = null)
    {
    // Determine type automatically if not provided
        if (!$type) {
            if ($user->email) $type = 'email';
            elseif ($user->phone) $type = 'phone';
            else throw new \Exception('User has neither email nor phone.');
        }

        $otp = $this->generateOtp($user, $type);

        if ($type === 'email') {
            $this->sendEmailOtp($user, $otp);
        } elseif ($type === 'phone') {
            // $this->sendSmsOtp($user, $otp);
        }
    }


    public function verifyOtp($user, $otp, $type)
    {
        $record = UserVerification::where('user_id', $user->id)
        ->where('otp', $otp)
        ->where('type', $type)
        ->where('is_verified', false)
        ->where('expires_at', '>=', now())
        ->first();

        if (!$record) {
            return false;
        }

        $record->update(['is_verified' => true]);
        return true;
    }
}
