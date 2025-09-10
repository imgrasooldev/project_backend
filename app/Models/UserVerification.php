<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    use HasFactory;

    // ✅ Add expires_at to fillable
    protected $fillable = ['user_id', 'otp', 'type', 'is_verified', 'expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
