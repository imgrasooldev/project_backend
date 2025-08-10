<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceProviderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'subcategory' => $this->subcategory ? [
                'id' => $this->subcategory->id,
                'name' => $this->subcategory->name,
            ] : null,
            'experience' => $this->experience,
            'available_days' => $this->available_days ?? null,
            'available_time' => $this->available_time ?? null,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'city_id' => $this->user->city_id,
                'bio' => $this->user->bio
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'area' => $this->area ? [
    'id' => $this->area->id,
    'name' => $this->area->name,
] : null,
        ];
    }
}
