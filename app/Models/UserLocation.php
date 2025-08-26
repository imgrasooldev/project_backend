<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'altitude',
        'speed',
        'city',
        'country',
        'address',
        'device_info',
        'location_timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
