<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class JobPostFilter extends ApiFilter
{
    // Allowed query parameters and operators
    protected $safeParms = [
    'categoryId' => ['eq', 'ne', 'in'],
    'subCategoryId' => ['eq', 'ne', 'in'],
    'seekerId' => ['eq', 'ne'],
    'providerId' => ['eq', 'ne'],
];


    // Map incoming query parameters to database columns
    protected $columnMap = [
        'categoryId' => 'category_id',
        'subCategoryId' => 'sub_category_id',
        'seekerId' => 'seeker_id',
        'providerId' => 'provider_id',
    ];

    // Map operators to SQL equivalents
    protected $operatorMap = [
    'eq' => '=',
    'ne' => '!=',
    'lt' => '<',
    'lte' => '<=',
    'gt' => '>',
    'gte' => '>=',
    'in' => 'in', // Add this
];

}
