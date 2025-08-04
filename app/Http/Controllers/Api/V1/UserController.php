<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Http\Resources\V1\UserResource;

class UserController extends BaseController
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function profile(Request $request)
    {
        $user = $this->userRepo->getAuthenticatedUser();

        if (!$user) {
            return $this->sendError('Unauthenticated.', [], 401);
        }

        $success = new UserResource($user);
        return $this->sendResponse($success, 'User profile fetched successfully.');
    }
}
