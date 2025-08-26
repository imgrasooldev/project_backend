<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\V1\UpdateLocationRequest;
use App\Models\UserLocation;
use Illuminate\Http\JsonResponse;

class LocationController extends BaseController
{
    public function update(UpdateLocationRequest $request): JsonResponse
    {
        try {
            $user = $request->user(); // from sanctum token

            $location = UserLocation::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'accuracy' => $request->accuracy,
                    'altitude' => $request->altitude,
                    'speed' => $request->speed,
                    'city' => $request->city,
                    'country' => $request->country,
                    'address' => $request->address,
                    'device_info' => $request->device_info,
                    'location_timestamp' => $request->timestamp,
                ]
            );

            return $this->sendResponse([
                'title' => $location->city ?? 'Unknown Location',
                'location' => $location,
            ], 'Location updated successfully');
        } catch (Exception $e) {
            return $this->sendError('Failed to update location.', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}