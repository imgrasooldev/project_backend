<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobApplicationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => parent::toArray($request),
            'pagination' => [
                'current_page'   => $this->currentPage(),
                'first_page_url' => $this->url(1),
                'prev_page_url'  => $this->previousPageUrl(),
                'next_page_url'  => $this->nextPageUrl(),
                'last_page_url'  => $this->url($this->lastPage()),
                'last_page'      => $this->lastPage(),
                'per_page'       => $this->perPage(),
                'total'          => $this->total(),
                'path'           => $this->path(),
                'range'          => $this->getUrlRange(1, $this->lastPage()),
                'last_item'      => $this->lastItem(),
            ],
        ];
    }
}
