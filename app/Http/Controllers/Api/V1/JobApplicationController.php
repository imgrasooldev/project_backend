<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\V1\StoreJobApplicationRequest;
use App\Http\Resources\V1\JobApplicationResource;
use App\Http\Resources\V1\JobApplicationCollection;
use App\Services\Api\V1\JobApplicationService;
use App\Repositories\Interfaces\JobApplicationRepositoryInterface;

class JobApplicationController extends BaseController
{
    protected $jobApplicationRepo;
    private $jobApplicationService;

    public function __construct(
        JobApplicationRepositoryInterface $jobApplicationRepo,
        JobApplicationService $jobApplicationService
    ) {
        $this->jobApplicationRepo = $jobApplicationRepo;
        $this->jobApplicationService = $jobApplicationService;
    }

    /**
     * List all job applications
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $applications = $this->jobApplicationRepo->all($perPage);

        return $this->sendResponse(
            new JobApplicationCollection($applications),
            'Job applications retrieved successfully.'
        );
    }

    /**
     * Store a new job application
     */
    public function store(StoreJobApplicationRequest $request)
    {
        try {
            $newApplication = $this->jobApplicationService->createApplicationFromProvider(
                $request->validated(),
                $request->user()
            );

            return $this->sendResponse(
                new JobApplicationResource($newApplication),
                'Job application submitted successfully.'
            );
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Show a specific job application
     */
    public function show($id)
    {
        $application = $this->jobApplicationRepo->find($id);

        return $this->sendResponse(
            new JobApplicationResource($application),
            'Job application fetched successfully.'
        );
    }

    /**
     * Update an existing job application
     */
    public function update(StoreJobApplicationRequest $request, $id)
    {
        try {
            $application = $this->jobApplicationRepo->find($id);

            $updatedApplication = $this->jobApplicationRepo->update($application, $request->validated());

            return $this->sendResponse(
                new JobApplicationResource($updatedApplication),
                'Job application updated successfully.'
            );
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Delete a job application
     */
    public function destroy($id)
    {
        try {
            $application = $this->jobApplicationRepo->find($id);
            $this->jobApplicationRepo->delete($application);

            return $this->sendResponse([], 'Job application deleted successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
 * Seeker approves a job application
 */
public function approve($id, Request $request)
{
     try {
        $application = $this->jobApplicationService->approveApplication($id, $request->user());

        return $this->sendResponse(
            new JobApplicationResource($application),
            'Job application approved successfully.'
        );
    } catch (\Exception $e) {
        return $this->sendError($e->getMessage(), [], 422);
    }
}

}
