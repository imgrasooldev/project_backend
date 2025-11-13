<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Sanctum check
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:500'],
            'experience'     => ['required', 'integer', 'min:0'],
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'available_time' => ['nullable', 'string', 'max:50'], // Not using in logic but allow storing
            'area_id'        => ['nullable', 'exists:areas,id'],  // Add this line
                        // ✔ newly added toggle field
            'is_active'      => ['nullable', 'boolean'],       // ✔ added
        ];
    }
}
