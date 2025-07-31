<?php

// app/Repositories/Eloquent/SubcategoryRepository.php
namespace App\Repositories\Eloquent;

use App\Models\Subcategory;
use App\Repositories\Interfaces\SubcategoryRepositoryInterface;

class SubcategoryRepository extends BaseRepository implements SubcategoryRepositoryInterface
{
    public function __construct(Subcategory $model)
    {
        parent::__construct($model);
    }

    // Add custom methods here if needed
}
