<?php

namespace App\Services;

use App\Helpers\FirebaseHelper;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

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
    
    /* public function sendNotification($deviceToken, $title, $body, $data = [])
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
            
            // ✅ Save notification record
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'device_token' => $deviceToken,
                'sent_at' => now(),
            ]);
            
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
    } */

        public function sendNotificationAndSave(array $data)
    {
        try {
            // ✅ Always save notification in DB
            $notification = Notification::create([
                'sender_id'        => $data['sender_id'] ?? null,
                'receiver_id'      => $data['receiver_id'] ?? null,
                'application_id'   => $data['application_id'] ?? null,
                'status'           => $data['status'] ?? 'pending',
                'title'            => $data['title'],
                'body'             => $data['body'],
                'data'             => $data['data'] ?? null,
                'device_token'     => $data['device_token'] ?? null,
                'sent_at'          => now(),
            ]);

            // ✅ Try sending push via Firebase
            if (!empty($data['device_token'])) {
                $accessToken = FirebaseHelper::getAccessToken();
                if (!$accessToken) {
                    throw new Exception("Failed to generate Firebase access token");
                }

                $message = [
                    "message" => [
                        "token" => $data['device_token'],
                        "notification" => [
                            "title" => $data['title'],
                            "body" => $data['body'],
                        ],
                        "data" => $data['data'] ?? [],
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

                if ($error || $httpCode !== 200) {
                    $notification->update(['status' => 'failed']);
                    Log::warning("FCM send failed for notification ID {$notification->id}: " . ($error ?: $response));
                }
            }

            return $notification;
        } catch (Exception $e) {
            Log::error("FirebaseService Error: " . $e->getMessage());
            return null;
        }
    }
}