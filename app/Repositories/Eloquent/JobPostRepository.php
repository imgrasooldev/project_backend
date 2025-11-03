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
        return JobPost::with(['seeker', 'provider', 'category', 'subCategory', 'applications.provider'])
        ->findOrFail($id);
    }

    public function filter(array $filters, $perPage = 10, array $queryParams = [], $userId = null)
    {
        $query = JobPost::with(['seeker', 'provider', 'category', 'subCategory'])->withCount('applications');

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

        // If userId is provided, eager load a flag for whether the user applied
    if ($userId) {
        $query->with(['applications' => function($q) use ($userId) {
            $q->where('provider_id', $userId);
        }]);
    }
         // Order by id descending
        $query->orderBy('id', 'desc');

        return $query->paginate($perPage)->appends($queryParams);
    }

    public function updateJobPost($id, array $data)
{
    $jobPost = $this->model->findOrFail($id);

    // Optional: Ensure only owner (seeker) can update
    if (isset($data['seeker_id']) && $jobPost->seeker_id !== $data['seeker_id']) {
        abort(403, 'Unauthorized to update this job post.');
    }

    $jobPost->update($data);

    return $jobPost;
}

}
