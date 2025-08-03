<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Filters\V1\ServiceProviderFilter;
use App\Http\Resources\V1\ServiceProviderCollection;
use App\Http\Controllers\Api\BaseController;
use App\Repositories\Interfaces\ServiceProviderRepositoryInterface;

class ServiceProviderController extends BaseController
{
    protected $serviceProviderRepo;

    public function __construct(ServiceProviderRepositoryInterface $serviceProviderRepo)
    {
        $this->serviceProviderRepo = $serviceProviderRepo;
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
}
