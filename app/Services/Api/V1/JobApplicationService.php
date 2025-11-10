<?php

namespace App\Services\Api\V1;

use App\Repositories\Interfaces\JobApplicationRepositoryInterface;
use App\Models\User;
use App\Models\JobPost;
use App\Services\FirebaseService;

class JobApplicationService
{
    protected $jobApplicationRepo;

    public function __construct(JobApplicationRepositoryInterface $jobApplicationRepo, FirebaseService $firebase)
    {
        $this->jobApplicationRepo = $jobApplicationRepo;
        $this->firebase = $firebase;
    }

    /**
     * Create a new job application from a provider
     *
     * @param array $inputData
     * @param User $user
     * @return \App\Models\JobApplication
     * @throws \Exception
     */
    public function createApplicationFromProvider(array $inputData, User $user)
    {
        $inputData['provider_id'] = $user->id;

        // Find the job post
        $jobPost = JobPost::findOrFail($inputData['job_post_id']);

        // Runtime default status
        $inputData['status'] = $inputData['status'] ?? 'pending';

        // Default applied_at if not provided
        $inputData['applied_at'] = $inputData['applied_at'] ?? now();

        // Optional cover letter & proposed budget defaults
        $inputData['cover_letter'] = $inputData['cover_letter'] ?? null;
        $inputData['proposed_budget'] = $inputData['proposed_budget'] ?? null;

        // Runtime generated description (optional, for logging or notification)
        $seekerName = $jobPost->seeker ? $jobPost->seeker->name : 'Customer';
        $inputData['note'] = "Provider {$user->name} applied to {$seekerName}'s job '{$jobPost->title}' with budget {$inputData['proposed_budget']}";

        // Check if provider already applied
        $existingApplication = $this->jobApplicationRepo->findByJobAndProvider($jobPost->id, $user->id);
        if ($existingApplication) {
            throw new \Exception('You have already applied to this job.');
        }

        return $this->jobApplicationRepo->create($inputData);
    }

    public function approveApplication($applicationId, User $seeker)
    {
        $application = $this->jobApplicationRepo->find($applicationId);

        // Ensure seeker is not the same as provider
        if ($application->provider_id === $seeker->id) {
            throw new \Exception('Provider and seeker cannot be the same user.');
        }

        // Ensure logged-in user is the job's seeker
        if ($application->jobPost->seeker_id !== $seeker->id) {
            throw new \Exception('You are not authorized to approve this application.');
        }


        $approvedApplication = $this->jobApplicationRepo->approveApplication($applicationId);

    // ✅ Send notification to provider
        $provider = $approvedApplication->provider;
        if ($provider && $provider->all_device_tokens) {
            foreach ($provider->all_device_tokens as $token) {
                $this->firebase->sendNotification(
                    $token,
                    "Application Accepted 🎉",
                    "Your application has been accepted.",
                    [
                        "application_id" => (string) $approvedApplication->id,   // ✅ cast to string
                        "job_post_id"    => (string) $approvedApplication->jobPost->id, // ✅ cast to string
                        "status"         => "accepted"
                    ]
                );

            }
        }

        return $approvedApplication;

    }

    public function withdrawApplication($applicationId, User $seeker)
    {
        $application = $this->jobApplicationRepo->find($applicationId);

    // Ensure seeker is the job owner
        if ($application->jobPost->seeker_id !== $seeker->id) {
            throw new \Exception('You are not authorized to withdraw this application.');
        }

        $withdrawnApplication = $this->jobApplicationRepo->withdrawApplication($applicationId);

    // ✅ Send notification to provider
        $provider = $withdrawnApplication->provider;
        if ($provider && $provider->all_device_tokens) {
            foreach ($provider->all_device_tokens as $token) {
                $this->firebase->sendNotification(
                    $token,
                    "Application Withdrawn ❌",
                    "Your accepted application for '{$withdrawnApplication->jobPost->title}' has been withdrawn by {$seeker->name}.",
                    [
                        "application_id" => (string) $withdrawnApplication->id,
                        "job_post_id"    => (string) $withdrawnApplication->jobPost->id,
                        "status"         => "withdrawn"
                    ]
                );
            }
        }

        return $withdrawnApplication;
    }


    public function updateApplicationStatus($applicationId, string $status, User $user)
{
    $application = $this->jobApplicationRepo->find($applicationId);

    $validStatuses = ['pending', 'accepted', 'rejected', 'withdrawn'];
    if (!in_array($status, $validStatuses)) {
        throw new \Exception('Invalid status provided.');
    }

    $jobPost = $application->jobPost;

    // ✅ Authorization: Only seeker can modify status
    if ($jobPost->seeker_id !== $user->id) {
        throw new \Exception('You are not authorized to update this application.');
    }

    // ✅ Update status using repository
    $updatedApplication = $this->jobApplicationRepo->updateStatus($applicationId, $status);

    // ✅ Determine message and job post status
    $notificationTitle = '';
    $notificationBody = '';
    $jobPostStatus = null;

    switch ($status) {
        case 'pending':
            $notificationTitle = "Application Pending ⏳";
            $notificationBody = "Your application for '{$jobPost->title}' is now pending.";
            $jobPostStatus = 'open';
            break;

        case 'accepted':
            $notificationTitle = "Application Accepted 🎉";
            $notificationBody = "Your application for '{$jobPost->title}' has been accepted.";
            $jobPostStatus = 'assigned';
            break;

        case 'rejected':
            $notificationTitle = "Application Rejected ❌";
            $notificationBody = "Your application for '{$jobPost->title}' has been rejected.";
            $jobPostStatus = 'open';
            break;

        case 'withdrawn':
            $notificationTitle = "Application Withdrawn 💤";
            $notificationBody = "Your application for '{$jobPost->title}' has been withdrawn by {$user->name}.";
            $jobPostStatus = 'open';
            break;
    }

    // ✅ Update job post if necessary
    if ($jobPostStatus) {
        $jobPost->status = $jobPostStatus;
        $jobPost->provider_id = ($status === 'accepted') ? $application->provider_id : null;
        $jobPost->save();
    }

    // ✅ Send Firebase notification to provider
    $provider = $updatedApplication->provider;
    if ($provider && $provider->all_device_tokens) {
        foreach ($provider->all_device_tokens as $token) {
            /* $this->firebase->sendNotification(
                $token,
                $notificationTitle,
                $notificationBody,
                [
                    'application_id' => (string) $updatedApplication->id,
                    'job_post_id'    => (string) $updatedApplication->jobPost->id,
                    'status'         => $status,
                ]
            ); */
            $this->firebase->sendNotificationAndSave([
    'sender_id'      => $user->id,
    'receiver_id'    => $provider->id,
    'application_id' => $updatedApplication->id,
    'status'         => $status,
    'title'          => $notificationTitle,
    'body'           => $notificationBody,
    'data'           => [
        'application_id' => (string) $updatedApplication->id,
        'job_post_id'    => (string) $updatedApplication->jobPost->id,
        'status'         => $status,
    ],
    'device_token'   => $token ?? null,
]);

        }
    }

    return $updatedApplication;
}



}