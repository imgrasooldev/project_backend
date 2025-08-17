<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'cover_letter'    => $this->cover_letter,
            'proposed_budget' => $this->proposed_budget,
            'status'          => $this->status,
            'applied_at'      => $this->applied_at,
            
            'provider' => [
                'id'    => $this->provider->id ?? null,
                'name'  => $this->provider->name ?? null,
                'phone' => $this->provider->phone ?? null,
            ],

            'job_post' => $this->jobPost ? [
                'id'          => $this->jobPost->id,
                'title'       => $this->jobPost->title,
                'description' => $this->jobPost->description,
                'desired_date'=> $this->jobPost->desired_date,
                'desired_time'=> $this->jobPost->desired_time,
            ] : null,
        ];
    }
}
