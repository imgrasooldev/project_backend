<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required'],
            'email_or_phone' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Check if value is email
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        if (\App\Models\User::where('email', $value)->exists()) {
                            $fail('The email has already been taken.');
                        }
                    }
                    // Check if value is Pakistani phone
                    elseif (preg_match('/^03[0-9]{9}$/', $value)) {
                        if (\App\Models\User::where('phone', $value)->exists()) {
                            $fail('The phone number has already been taken.');
                        }
                    }
                    // Neither valid email nor valid phone
                    else {
                        $fail('The :attribute must be a valid email or Pakistani phone number.');
                    }
                }
            ],
            'password' => ['required', 'min:6'],
            'confirmPassword' => ['required', 'same:password'],
        ];
    }

    protected function prepareForValidation()
    {
        $emailOrPhone = $this->input('email_or_phone');

        if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
            $this->merge([
                'email' => $emailOrPhone,
                'phone' => null
            ]);
        } elseif (preg_match('/^03[0-9]{9}$/', $emailOrPhone)) {
            $this->merge([
                'phone' => $emailOrPhone,
                'email' => null
            ]);
        }

        $this->merge([
            'confirm_password' => $this->confirmPassword
        ]);
    }
}