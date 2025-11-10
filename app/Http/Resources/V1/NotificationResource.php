<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'data' => $this->data,
            'sent_at' => $this->sent_at,
            'read_by_receiver' => $this->read_by_receiver,
            'read_by_receiver_at' => $this->read_by_receiver_at,
            'application' => $this->application ? [
                'id' => $this->application->id,
                'status' => $this->application->status,
            ] : null,
            'sender' => $this->sender ? [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'email' => $this->sender->email,
            ] : null,
            'receiver' => $this->receiver ? [
                'id' => $this->receiver->id,
                'name' => $this->receiver->name,
                'email' => $this->receiver->email,
            ] : null,
        ];
    }
}
