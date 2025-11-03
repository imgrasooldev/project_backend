<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationGroupedResource extends JsonResource
{
    // public function toArray($request): array
    // {
    //     return [
    //         'id'              => $this->id,
    //         'job_post_id'     => $this->job_post_id,
    //         'cover_letter'    => $this->cover_letter,
    //         'proposed_budget' => $this->proposed_budget,
    //         'status'          => $this->status,
    //         'applied_at'      => $this->applied_at,

    //         'job_post' => $this->whenLoaded('jobPost', function () {
    //             return [
    //                 'id'            => $this->jobPost->id,
    //                 'title'         => $this->jobPost->title,
    //                 'description'   => $this->jobPost->description,
    //                 'budget'        => $this->jobPost->budget,
    //                 'address'       => $this->jobPost->address,
    //                 'desired_date'  => $this->jobPost->desired_date,
    //                 'desired_time'  => $this->jobPost->desired_time,
    //                 'status'        => $this->jobPost->status,
    //                 'seeker'        => [
    //                     'id'    => $this->jobPost->seeker->id ?? null,
    //                     'name'  => $this->jobPost->seeker->name ?? null,
    //                     'phone' => $this->jobPost->seeker->phone ?? null,
    //                 ],
    //             ];
    //         }),
    //     ];
    // }
    public function toArray($request): array
{
    $jobPost = $this->jobPost;

    return [
        'id'                => $this->id,
        'job_post_id'       => $this->job_post_id,
        'cover_letter'      => $this->cover_letter,
        'proposed_budget'   => $this->proposed_budget,
        'status'            => $this->status,
        'applied_at'        => $this->applied_at,

        'provider' => $this->provider ? [
            'id'    => $this->provider->id,
            'name'  => $this->provider->name,
            'phone' => $this->provider->phone,
        ] : null,

        'job_post' => $jobPost ? [
            'id'                => $jobPost->id,
            'title'             => $jobPost->title,
            'description'       => $jobPost->description,
            'budget'            => $jobPost->budget,
            'address'           => $jobPost->address,
            'desired_date'      => $jobPost->desired_date,
            'desired_time'      => $jobPost->desired_time,
            'status'            => $jobPost->status,
            'urgency'           => $jobPost->type ?? null,
            'category'          => $jobPost->category ? [
                'id'    => $jobPost->category->id,
                'name'  => $jobPost->category->name
            ] : null,
            'subCategory'       => $jobPost->subCategory ? [
                'id'    => $jobPost->subCategory->id,
                'name'  => $jobPost->subCategory->name
            ] : null,
            'applicationsCount' => $jobPost->applications()->count(),
            'userApplied'       => $jobPost->seeker ? [
                'id'    => $jobPost->seeker->id,
                'name'  => $jobPost->seeker->name,
                'phone' => $jobPost->seeker->phone
            ] : null,
        ] : null,
    ];
}


}
