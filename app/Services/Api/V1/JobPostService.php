<?php

namespace App\Services\Api\V1;

use App\Repositories\Interfaces\JobPostRepositoryInterface;
use App\Models\User;
use App\Models\Subcategory;

class JobPostService
{
    protected $jobPostRepo;

    public function __construct(JobPostRepositoryInterface $jobPostRepo)
    {
        $this->jobPostRepo = $jobPostRepo;
    }

    public function createJobPostFromUser(array $inputData, User $user)
    {
        $inputData['seeker_id'] = $user->id;

    // Get category from subcategory
        $subcategory = Subcategory::findOrFail($inputData['sub_category_id']);
        $inputData['category_id'] = $subcategory->category_id;

        // Default type = "post"
        $inputData['type'] = $inputData['type'] ?? 'posted';


        if (($inputData['type'] ?? 'posted') === 'direct') {
  // Find provider
            $provider = User::find($inputData['provider_id']);
            $providerName = $provider ? $provider->name : 'Provider';

    // Find subcategory
            $subCategory = SubCategory::find($inputData['sub_category_id']);
            $subCategoryName = $subCategory ? $subCategory->name : 'Service';

    // Find seeker (customer)
            $seeker = User::find($inputData['seeker_id']);
            $seekerName = $seeker ? $seeker->name : 'Customer';

    // Runtime generated title & description
            $inputData['title'] = "{$seekerName} is requesting {$subCategoryName} service from {$providerName}";
            $inputData['description'] = "{$seekerName} is directly requesting a {$subCategoryName} service from {$providerName} on {$inputData['desired_date']} at {$inputData['desired_time']}.";
        } else {
            $inputData['title'] = $inputData['title'] ?? 'Untitled Job Post';
            $inputData['description'] = $inputData['description'] ?? 'No description provided.';
        }



        // If direct request, provider_id must be set
        if ($inputData['type'] === 'direct' && empty($inputData['provider_id'])) {
            throw new \Exception('Provider ID is required for direct requests.');
        }


        return $this->jobPostRepo->create($inputData);
    }
}
