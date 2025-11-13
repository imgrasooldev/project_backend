<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPost extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'seeker_id',
        'provider_id',
        'category_id',
        'sub_category_id',
        'desired_date',
        'desired_time',
        'type',
        'status',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function seeker()
    {
        return $this->belongsTo(User::class, 'seeker_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(Subcategory::class, 'sub_category_id');
    }
    public function applications()
{
    return $this->hasMany(JobApplication::class, 'job_post_id');
}
}
