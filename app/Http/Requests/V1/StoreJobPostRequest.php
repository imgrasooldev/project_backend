<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'sub_category_id' => ['required', 'exists:subcategories,id'],
            'desired_date'    => ['nullable', 'date'],
            'desired_time'    => ['nullable', 'date_format:H:i'],
        'provider_id'     => ['nullable', 'exists:users,id'], // only for direct requests
        'status'          => ['nullable', 'in:open,assigned,completed,cancelled'],
        'type'            => ['nullable', 'in:direct,posted'],
    ];

    // if request type is posted → require title & description
    if ($this->input('type') === 'posted') {
        $rules['title'] = ['required', 'string', 'max:255'];
        $rules['description'] = ['required', 'string'];
    }

    return $rules;
}

}
