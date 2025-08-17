<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Filters\V1\JobPostFilter;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\V1\StoreJobPostRequest;
use App\Http\Resources\V1\JobPostResource;
use App\Http\Resources\V1\JobPostCollection;
use App\Services\Api\V1\JobPostService;
use App\Repositories\Interfaces\JobPostRepositoryInterface;

class JobPostController extends BaseController
{
    protected $jobPostRepo;
    private $jobPostService;

    public function __construct(JobPostRepositoryInterface $jobPostRepo, JobPostService $jobPostService)
    {
        $this->jobPostRepo = $jobPostRepo;
        $this->jobPostService = $jobPostService;
    }

    public function index(Request $request)
    {
        $filter = new JobPostFilter();
        $filterItems = $filter->transform($request);
        $perPage = $request->input('per_page', 10);

        $jobPosts = $this->jobPostRepo->filter($filterItems, $perPage, $request->query());

        return $this->sendResponse(
            new JobPostCollection($jobPosts),
            'Job Posts retrieved successfully.'
        );
    }

    public function show($id)
    {
        $post = $this->jobPostRepo->find($id);

        return $this->sendResponse(new JobPostResource($post), 'Job post fetched successfully.');
    }

    public function store(StoreJobPostRequest $request)
    {
       try {
        $newPost = $this->jobPostService->createJobPostFromUser(
            $request->validated(),
            $request->user()
        );

        return $this->sendResponse(
            new JobPostResource($newPost),
            $newPost->type === 'direct'
            ? 'Direct request created successfully.'
            : 'Job post created successfully.'
        );
    } catch (\Exception $e) {
        return $this->sendError($e->getMessage(), [], 422);
    }
}

public function getUserJobPosts(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return $this->sendError('Unauthorized', [], 401);
    }

    $perPage = $request->input('per_page', 10);

        // Assuming user has a relation `jobPosts()`
    $posts = $user->jobPosts()->paginate($perPage);

    $success = new JobPostCollection($posts);

    return $this->sendResponse($success, 'User job posts retrieved successfully.');
}

public function otherUserOffer(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return $this->sendError('Unauthorized', [], 401);
    }

    $filter = new JobPostFilter();
    $filterItems = $filter->transform($request);
    $perPage = $request->input('per_page', 10);

    // Exclude logged-in user's posts
    $filterItems[] = ['seekerId', 'ne', $user->id];

    // Only posts where provider_id is null (unassigned)
    $filterItems[] = ['providerId', 'eq', null];

    $jobPosts = $this->jobPostRepo->filter($filterItems, $perPage, $request->query());

    return $this->sendResponse(
        new JobPostCollection($jobPosts),
        'Job posts excluding your own and unassigned retrieved successfully.'.$user->id
    );
}

public function serviceRequest(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return $this->sendError('Unauthorized', [], 401);
    }

    $perPage = $request->input('per_page', 10);

    // Filter for jobs where seeker_id = logged-in user
    $filter = [
        ['seekerId', 'eq', $user->id],
    ];

    $jobPosts = $this->jobPostRepo->filter($filter, $perPage, $request->query());

    return $this->sendResponse(
        new JobPostCollection($jobPosts),
        'Your job posts retrieved successfully.'
    );
}




}