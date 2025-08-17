<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'job_post_id'     => ['required', 'exists:job_posts,id'],
            'cover_letter'    => ['nullable', 'string', 'max:2000'],
            'proposed_budget' => ['nullable', 'numeric', 'min:0'],
            'status'          => ['nullable', 'in:pending,accepted,rejected,withdrawn'],
            'applied_at'      => ['nullable', 'date'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $jobPostId = $this->input('job_post_id');
            $userId = $this->user()->id;

            $jobPost = \App\Models\JobPost::find($jobPostId);
            if ($jobPost && $jobPost->seeker_id === $userId) {
                $validator->errors()->add('job_post_id', 'You cannot apply to your own job.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'job_post_id.required' => 'The job post is required.',
            'job_post_id.exists'   => 'The selected job post does not exist.',
            'cover_letter.max'     => 'Cover letter cannot exceed 2000 characters.',
            'proposed_budget.numeric' => 'Proposed budget must be a number.',
            'proposed_budget.min'  => 'Proposed budget must be at least 0.',
            'status.in'            => 'Status must be pending, accepted, rejected, or withdrawn.',
            'applied_at.date'      => 'Applied date must be a valid date.',
        ];
    }
}
