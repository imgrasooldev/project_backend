<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $table = 'job_applications';

    protected $fillable = [
        'job_post_id',
        'provider_id',
        'cover_letter',
        'proposed_budget',
        'status',
        'applied_at',
    ];

    /**
     * Relationship: Job Application belongs to a Job Post
     */
    public function jobPost()
    {
        return $this->belongsTo(JobPost::class, 'job_post_id');
    }

    /**
     * Relationship: Job Application belongs to a Provider (User)
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
