<?php

namespace App\Repositories\Interfaces;

interface JobApplicationRepositoryInterface
{
    public function create(array $data);
    public function find($id);
    public function all($perPage = 10);
    public function filter(array $filters, $perPage = 10, array $queryParams = []);
    public function findByJobAndProvider($jobPostId, $providerId); // optional helper
    public function approveApplication($applicationId);
    public function getByProviderGroupedByStatus($providerId);

}
