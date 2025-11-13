<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceProfile;
use App\Models\Subcategory; // ✅ Add this
use App\Repositories\Interfaces\ServiceProviderRepositoryInterface;

class ServiceProviderRepository extends BaseRepository implements ServiceProviderRepositoryInterface
{
    public function __construct(ServiceProfile $model)
{
    parent::__construct($model);
}
 public function all($perPage = null) // match the default value
{
    return ServiceProfile::with(['user', 'category', 'subcategory', 'area'])
        ->paginate($perPage);
}


    public function find($id){
        return ServiceProfile::with(['user', 'category', 'subcategory', 'area'])->findOrFail($id);
    }

    public function filter(array $filters, $perPage){
        $query = ServiceProfile::with(['user', 'category', 'subcategory', 'area']);
        foreach ($filters as $filter) {
            $query->where($filter[0], $filter[1], $filter[2]);
        }
        $query->orderBy('id', 'desc');  // Add this line
        return $query->paginate($perPage);
    }

    public function createWithCategory(array $data)
    {
        // Resolve category from subcategory
        $subcategory = Subcategory::findOrFail($data['subcategory_id']);
        $data['category_id'] = $subcategory->category_id;

        return $this->create($data); // ✅ BaseRepository method
    }
    public function findWithRelations($id)
{
    return $this->model->with(['user', 'category', 'subcategory', 'area'])->findOrFail($id);
}

public function updateWithCategory($id, array $data)
{
    $serviceProfile = $this->model->findOrFail($id);

    // Optional: Ensure only the owner can update
    if (isset($data['user_id']) && $serviceProfile->user_id !== $data['user_id']) {
        abort(403, 'Unauthorized to update this service.');
    }

    // Resolve category from subcategory if provided
    if (isset($data['subcategory_id'])) {
        $subcategory = \App\Models\Subcategory::findOrFail($data['subcategory_id']);
        $data['category_id'] = $subcategory->category_id;
    }

    $serviceProfile->update($data);

    return $serviceProfile;
}

public function delete($id)
{
    $model = $this->find($id);
    return $model->delete();
}




}
