<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProfile extends Model
{
    use HasFactory;

    protected $casts = [
        'available_days' => 'array',
    ];
    // app/Models/ServiceProfile.php

    protected $fillable = [
        'user_id',
        'title',
        'category_id',
        'subcategory_id',
        'experience',
        'available_days',
        'available_time',
        'area_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
