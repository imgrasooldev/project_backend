<?php



namespace App\Http\Resources\v1;



use Illuminate\Http\Request;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserVerification;



class UserResource extends JsonResource

{

    /**

     * Transform the resource into an array.

     *

     * @return array<string, mixed>

     */

    public function toArray(Request $request): array

    {
        // Check if user has verified OTP (email or phone)
        $verification = UserVerification::where('user_id', $this->id)
            ->where('is_verified', true)
            ->first();

        // Determine OTP type (phone/email) if unverified
        $otpType = null;
        if (!$verification) {
            if ($this->email) $otpType = 'email';
            elseif ($this->phone) $otpType = 'phone';
        }


        return [

            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone' => $this->phone,
            'city_id' => $this->city_id,
            'bio' => $this->bio,
            'is_verified' => $verification ? true : false,
            'otp_type' => $otpType

        ];

    }

}

