<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class ServiceProviderFilter extends ApiFilter
{
    protected $safeParms = [
        'subcategoryId' => ['eq'],
        'areaId' => ['eq'],
        'isActive' => ['eq']
    ];

    protected $columnMap = [
        'subcategoryId' => 'subcategory_id',  // ← FIXED this line
        'areaId' => 'area_id',
        'isActive' => 'is_active'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}

