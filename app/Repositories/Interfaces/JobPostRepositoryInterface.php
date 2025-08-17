<?php

namespace App\Repositories\Interfaces;

interface JobPostRepositoryInterface
{
    public function create(array $data);
    public function find($id);
    public function all($perPage);
    public function filter(array $filters, $perPage);
}
