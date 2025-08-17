<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class JobApplicationFilter extends ApiFilter
{
    // Allowed query parameters and operators
    protected $safeParms = [
        'jobPostId'   => ['eq', 'ne', 'in'],
        'seekerId'    => ['eq', 'ne', 'in'],
        'providerId'  => ['eq', 'ne', 'in'],
        'status'      => ['eq', 'ne', 'in'],
    ];

    // Map incoming query parameters to database columns
    protected $columnMap = [
        'jobPostId'   => 'job_post_id',
        'seekerId'    => 'seeker_id',
        'providerId'  => 'provider_id',
        'status'      => 'status',
    ];

    // Map operators to SQL equivalents
    protected $operatorMap = [
        'eq'  => '=',
        'ne'  => '!=',
        'lt'  => '<',
        'lte' => '<=',
        'gt'  => '>',
        'gte' => '>=',
        'in'  => 'in',
    ];
}
