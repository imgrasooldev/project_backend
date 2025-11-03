<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobApplicationGroupedCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'pending'   => JobApplicationGroupedResource::collection($this->get('pending', collect())),
            'accepted'  => JobApplicationGroupedResource::collection($this->get('accepted', collect())),
            'rejected'  => JobApplicationGroupedResource::collection($this->get('rejected', collect())),
            'withdrawn' => JobApplicationGroupedResource::collection($this->get('withdrawn', collect())),
        ];
    }
}
