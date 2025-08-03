<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceProfile;
use App\Repositories\Interfaces\ServiceProviderRepositoryInterface;

class ServiceProviderRepository implements ServiceProviderRepositoryInterface
{
    public function all($perPage)
    {
        return ServiceProfile::with(['user', 'category'])->paginate($perPage);
    }

    public function find($id)
    {
        return ServiceProfile::with(['user', 'category'])->findOrFail($id);
    }

    public function filter(array $filters, $perPage)
    {
        $query = ServiceProfile::with(['user', 'category']);
        
        foreach ($filters as $filter) {
            $query->where($filter[0], $filter[1], $filter[2]);
        }

        return $query->paginate($perPage);
    }
}
