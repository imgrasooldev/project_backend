<?php

namespace App\Services\Api\V1;

use App\Repositories\Interfaces\ServiceProviderRepositoryInterface;
use App\Models\Subcategory;
use App\Models\User;

class ServiceProfileService
{
    protected $serviceProviderRepo;

    public function __construct(ServiceProviderRepositoryInterface $serviceProviderRepo)
    {
        $this->serviceProviderRepo = $serviceProviderRepo;
    }

    public function createServiceProfile(array $data)
    {
    // Core logic to create a service profile including any business rules
        $serviceProfile = $this->serviceProviderRepo->createWithCategory($data);

        return $this->serviceProviderRepo->findWithRelations($serviceProfile->id);
    }
    public function createServiceProfileFromUser(array $inputData, User $user)
    {
        $inputData['user_id'] = $user->id;

        return $this->createServiceProfile($inputData);
    }


    public function updateServiceProfile($id, array $data)
{
    // Ensure subcategory belongs to a category
    if (isset($data['subcategory_id'])) {
        $subcategory = \App\Models\Subcategory::findOrFail($data['subcategory_id']);
        $data['category_id'] = $subcategory->category_id;
    }

    // Perform the update
    $serviceProfile = $this->serviceProviderRepo->updateWithCategory($id, $data);

    return $this->serviceProviderRepo->findWithRelations($serviceProfile->id);
}

public function updateServiceProfileFromUser($id, array $data, User $user)
{
    $data['user_id'] = $user->id;
    return $this->updateServiceProfile($id, $data);
}


public function deleteServiceProfile($id, $userId)
{
    $service = $this->serviceProviderRepo->find($id);

    if ($service->user_id !== $userId) {
        abort(403, 'You are not allowed to delete this service.');
    }

    $service->delete();

    return true;
}

public function deleteServiceProfileFromUser($id, User $user)
{
    return $this->deleteServiceProfile($id, $user->id);
}



}
