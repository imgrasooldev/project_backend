<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class JobPostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'desired_date'  => $this->desired_date,
            'desired_time'  => $this->desired_time,
            'seeker' => [
                'id'    => $this->seeker->id,
                'name'  => $this->seeker->name,
                'phone' => $this->seeker->phone,
            ],
            'provider' => $this->provider ? [
                'id'    => $this->provider->id,
                'name'  => $this->provider->name,
                'phone' => $this->provider->phone,
            ] : null,
            'category' => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ],
            'sub_category' => [
                'id'   => $this->subCategory->id,
                'name' => $this->subCategory->name,
            ],
        ];
    }
}
