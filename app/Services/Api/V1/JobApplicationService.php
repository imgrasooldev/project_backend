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

        UserVerification::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'type' => $type
        ]);

        return $otp;
    }

    public function sendEmailOtp($user, $otp)
    {
        Mail::to($user->email)->send(new SendOtpEmailForRegister($user, $otp));
    }

    public function sendSmsOtp($user, $otp)
    {
        // Replace this with your SMS service logic
        // Example: SmsService::send($user->phone, "Your OTP is: $otp");
    }

    public function sendOtp($user)
    {
        if ($user->email) {
            $otp = $this->generateOtp($user, 'email');
            $this->sendEmailOtp($user, $otp);
        } elseif ($user->phone) {
            $otp = $this->generateOtp($user, 'phone');
            $this->sendSmsOtp($user, $otp);
        }
    }
}
