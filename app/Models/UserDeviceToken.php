<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_token',
        'device_type',
        'device_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
