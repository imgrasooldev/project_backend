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


}
