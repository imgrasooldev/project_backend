<?php

namespace App\Repositories\Eloquent;

use App\Models\JobApplication;
use App\Repositories\Interfaces\JobApplicationRepositoryInterface;

class JobApplicationRepository extends BaseRepository implements JobApplicationRepositoryInterface
{
    public function __construct(JobApplication $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all job applications with relationships
     */
    public function all($perPage = 10)
    {
        return JobApplication::with(['jobPost', 'provider'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find a job application by ID with relationships
     */
    public function find($id)
    {
        return JobApplication::with(['jobPost', 'provider'])
            ->findOrFail($id);
    }

    /**
     * Filter job applications dynamically
     */
    public function filter(array $filters, $perPage = 10, array $queryParams = [])
    {
        $query = JobApplication::with(['jobPost', 'provider']);

        $columnMap = [
            'jobPostId'   => 'job_post_id',
            'providerId'  => 'provider_id',
            'status'      => 'status',
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

        $query->orderBy('id', 'desc');

        return $query->paginate($perPage)->appends($queryParams);
    }

    public function findByJobAndProvider($jobPostId, $providerId)
{
    return JobApplication::where('job_post_id', $jobPostId)
                         ->where('provider_id', $providerId)
                         ->first();
}

public function approveApplication($applicationId)
{
    $application = $this->find($applicationId);

    $application->status = 'accepted';
    $application->save();

    $jobPost = $application->jobPost;
    $jobPost->provider_id = $application->provider_id;
    $jobPost->status = 'assigned';
    $jobPost->save();

    return $application;
}




}
