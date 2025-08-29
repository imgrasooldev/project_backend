<?php

namespace App\Services;

use App\Helpers\FirebaseHelper;
use Exception;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $projectId;
    
    public function __construct()
    {
        // Get project ID from service account file
        $jsonKey = storage_path('app/firebase/kaam-staging.json');
        if (file_exists($jsonKey)) {
            $serviceAccount = json_decode(file_get_contents($jsonKey), true);
            $this->projectId = $serviceAccount['project_id'] ?? 'kaam-staging-0dd69';
        } else {
            $this->projectId = 'kaam-staging-0dd69';
        }
    }
    
    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        try {
            $accessToken = FirebaseHelper::getAccessToken();
            
            if (!$accessToken) {
                throw new Exception("Failed to generate access token");
            }

            $message = [
                "message" => [
                    "token" => $deviceToken,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                    "data" => $data,
                ]
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $accessToken,
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS => json_encode($message),
                CURLOPT_TIMEOUT => 30,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                throw new Exception("FCM request cURL error: " . $error);
            }
            
            $responseData = json_decode($response, true);
            
            if ($httpCode !== 200) {
                $errorMsg = $responseData['error']['message'] ?? 'Unknown error';
                throw new Exception("FCM request failed with HTTP code: " . $httpCode . ". Error: " . $errorMsg);
            }
            
            return [
                'success' => true,
                'message_id' => $responseData['name'] ?? null,
                'response' => $responseData
            ];
            
        } catch (Exception $e) {
            Log::error('FirebaseService Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}