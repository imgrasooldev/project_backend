<?php

// app/Repositories/Interfaces/SubcategoryRepositoryInterface.php
namespace App\Repositories\Interfaces;

interface SubcategoryRepositoryInterface
{
    public function all($paginate = null);
}
