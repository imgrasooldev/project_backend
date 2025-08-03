<?php

namespace App\Repositories\Interfaces;

interface ServiceProviderRepositoryInterface
{
    public function all($perPage);
    public function find($id);
    public function filter(array $filters, $perPage);
}
