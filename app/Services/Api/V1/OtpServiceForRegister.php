<?php

namespace App\Services\Api\V1;

use App\Models\UserVerification;
use App\Mail\SendOtpEmailForRegister;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OtpServiceForRegister
{
    /**
     * Generate OTP for a user and store/update in DB with expiry
     */
    public function generateOtp($user, $type = 'email')
    {
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        
        // Convert to proper timestamp format for database
        $expiresAtFormatted = Carbon::parse($expiresAt)->format('Y-m-d H:i:s');

        // Check if an unverified OTP exists for this user/type
        $verification = UserVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_verified', false)
            ->first();

        if ($verification) {
            // Update existing record
            $verification->update([
                'otp' => $otp,
                'expires_at' => $expiresAtFormatted, // Use formatted timestamp
            ]);
        } else {
            // Create new record if none exists
            UserVerification::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'type' => $type,
                'expires_at' => $expiresAtFormatted, // Use formatted timestamp
                'is_verified' => false,
            ]);
        }

        return $otp;
    }

    /**
     * Send OTP via Email
     */
    public function sendEmailOtp($user, $otp)
    {
        Mail::to($user->email)->queue(new SendOtpEmailForRegister($user, $otp));
    }

    /**
     * Send OTP via SMS (Twilio example)
     */
    public function sendSmsOtp($user, $otp)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_NUMBER');
        
        $client = new \Twilio\Rest\Client($sid, $token);
        $client->messages->create($user->phone, [
            'from' => $from,
            'body' => "Your OTP is: $otp"
        ]);
    }

    /**
     * Send OTP (Email/Phone) to user
     */
    public function sendOtp($user, $type = null)
    {
        // Auto-detect type if not passed
        if (!$type) {
            if ($user->email) {
                $type = 'email';
            } elseif ($user->phone) {
                $type = 'phone';
            } else {
                throw new \Exception('User has neither email nor phone.');
            }
        }
        
        $otp = $this->generateOtp($user, $type);
        
        if ($type === 'email') {
            $this->sendEmailOtp($user, $otp);
        } elseif ($type === 'phone') {
            // $this->sendSmsOtp($user, $otp);
        }
    }

    /**
     * Verify OTP for a user
     */
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

    /**
     * Resend OTP to user
     */
    public function resendOtp($user, $type = null)
    {
        // Auto-detect type if not provided
        if (!$type) {
            if ($user->email) $type = 'email';
            elseif ($user->phone) $type = 'phone';
            else throw new \Exception('User has neither email nor phone.');
        }

        // Generate new OTP and send
        $this->sendOtp($user, $type);
    }
}