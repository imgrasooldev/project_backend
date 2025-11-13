<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Filters\V1\ServiceProviderFilter;
use App\Http\Resources\V1\ServiceProviderCollection;
use App\Http\Controllers\Api\BaseController;
use App\Repositories\Interfaces\ServiceProviderRepositoryInterface;
use App\Http\Requests\V1\StoreServiceProfileRequest;
use App\Http\Resources\V1\ServiceProviderResource;
use App\Services\Api\V1\ServiceProfileService;

class ServiceProviderController extends BaseController
{
    protected $serviceProviderRepo;
    private $serviceProfileService;

    public function __construct(ServiceProviderRepositoryInterface $serviceProviderRepo, 
        ServiceProfileService $serviceProfileService)
    {
        $this->serviceProviderRepo = $serviceProviderRepo;
        $this->serviceProfileService = $serviceProfileService;
    }

    public function index(Request $request)
    {
        $filter = new ServiceProviderFilter();

        $filterItems = $filter->transform($request); //[['column', 'operator', 'value']]
        $perPage = $request->input('per_page', 10); // Optional pagination size

        $serviceProviders = $this->serviceProviderRepo->filter($filterItems, $perPage);

        $success = new ServiceProviderCollection($serviceProviders->appends($request->query()));

        return $this->sendResponse($success, 'Service providers retrieved successfully.');
    }

    public function show($id)
    {
        $serviceProvider = $this->serviceProviderRepo->find($id);

        return $this->sendResponse($serviceProvider, 'Service Provider fetched successfully.');
    }

    public function store(StoreServiceProfileRequest $request)
{
    $newService = $this->serviceProfileService->createServiceProfileFromUser(
        $request->validated(),
        $request->user()
    );

    return $this->sendResponse(
        new ServiceProviderResource($newService),
        'Service created successfully.'
    );
}

public function update(StoreServiceProfileRequest $request, $id)
{
    $updatedService = $this->serviceProfileService->updateServiceProfileFromUser(
        $id,
        $request->validated(),
        $request->user()
    );

    return $this->sendResponse(
        new ServiceProviderResource($updatedService),
        'Service updated successfully.'
    );
}

    public function getUserServices(Request $request){
        // Get the authenticated user from Sanctum token
        $user = $request->user();

        if (!$user) {
            return $this->sendError('Unauthorized', [], 401);
        }

        // Optional: Pagination size
        $perPage = $request->input('per_page', 10);

        // Fetch the services related to the logged-in user
        // Adjust the relation name (services) according to your model relationship
        $services = $user->services()->paginate($perPage);

        // Wrap in collection if you’re using a Resource/Collection
        $success = new ServiceProviderCollection($services);

        return $this->sendResponse($success, 'User services retrieved successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $this->serviceProfileService->deleteServiceProfileFromUser($id, $request->user());

        return $this->sendResponse([], 'Service deleted successfully.');
    }


    public function toggleStatus(Request $request, $id)
    {
        $service = $this->serviceProviderRepo->find($id);

        if ($service->user_id !== $request->user()->id) {
            return $this->sendError("Unauthorized", [], 403);
        }

        $service->is_active = !$service->is_active;
        $service->save();

        return $this->sendResponse(
            new ServiceProviderResource($service),
            "Service status updated successfully."
        );
    }



}
