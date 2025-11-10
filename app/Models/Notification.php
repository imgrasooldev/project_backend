<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'application_id',
        'status',
        'title',
        'body',
        'data',
        'device_token',
        'sent_at',
        'read_by_sender',
        'read_by_sender_at',
        'read_by_receiver',
        'read_by_receiver_at',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'read_by_sender_at' => 'datetime',
        'read_by_receiver_at' => 'datetime',
    ];

    /* 🔹 Relationships */

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'application_id');
    }

    /* 🔹 Helper scopes (optional) */

    public function scopeForReceiver($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->where('read_by_receiver', false);
    }
}
