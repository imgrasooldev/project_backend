<?php

namespace App\Repositories\Eloquent;

use App\Models\JobPost;
use App\Repositories\Interfaces\JobPostRepositoryInterface;

class JobPostRepository extends BaseRepository implements JobPostRepositoryInterface
{
    public function __construct(JobPost $model)
    {
        parent::__construct($model);
    }

    public function all($perPage = 10)
    {
        return JobPost::with(['seeker', 'provider', 'category', 'subCategory'])
        ->paginate($perPage);
    }

    public function find($id)
    {
        return JobPost::with(['seeker', 'provider', 'category', 'subCategory'])
        ->findOrFail($id);
    }

    public function filter(array $filters, $perPage = 10, array $queryParams = [])
    {
        $query = JobPost::with(['seeker', 'provider', 'category', 'subCategory']);

        $columnMap = [
            'seekerId'      => 'seeker_id',
            'providerId'    => 'provider_id',
            'categoryId'    => 'category_id',
            'subCategoryId' => 'sub_category_id',
        ];

        foreach ($filters as [$field, $operator, $value]) {
            $column = $columnMap[$field] ?? $field;

            if ($operator === 'in') {
                $values = is_array($value) ? $value : explode(',', $value);
                $query->whereIn($column, $values);
            } elseif ($operator === 'ne') {
                $query->where($column, '!=', $value)
                ->whereNotNull($column);
            } elseif ($operator === 'eq') {
                if (is_null($value)) {
                    $query->whereNull($column);
                } else {
                    $query->where($column, '=', $value);
                }
            }

        }
         // Order by id descending
        $query->orderBy('id', 'desc');

        return $query->paginate($perPage)->appends($queryParams);
    }
}
