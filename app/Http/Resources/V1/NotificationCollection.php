<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'items' => NotificationResource::collection($this->collection),
            'pagination' => [
                "current_page" => $this->currentPage(),
                "first_page_url" => $this->url(1),
                "prev_page_url" => $this->previousPageUrl(),
                "next_page_url" => $this->nextPageUrl(),
                "last_page" => $this->lastPage(),
                "per_page" => $this->perPage(),
                "total" => $this->total(),
            ],
        ];
    }
}
