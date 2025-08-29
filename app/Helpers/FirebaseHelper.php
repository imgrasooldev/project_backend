<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;

class FirebaseHelper
{
    public static function getAccessToken()
    {
        try {
            $jsonKey = storage_path('app/firebase/kaam-staging.json');
            
            if (!file_exists($jsonKey)) {
                throw new Exception("Firebase service account file not found at: " . $jsonKey);
            }
            
            $serviceAccount = json_decode(file_get_contents($jsonKey), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON in service account file");
            }

            // Validate required fields
            $requiredFields = ['client_email', 'private_key', 'token_uri', 'project_id'];
            foreach ($requiredFields as $field) {
                if (empty($serviceAccount[$field])) {
                    throw new Exception("Missing required field in service account: " . $field);
                }
            }

            $now = time();
            $exp = $now + 3600; // 1 hour expiration

            // Create JWT payload
            $payload = [
                "iss" => $serviceAccount['client_email'],
                "scope" => "https://www.googleapis.com/auth/firebase.messaging",
                "aud" => $serviceAccount['token_uri'],
                "iat" => $now,
                "exp" => $exp,
            ];

            $jwtHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])));
            $jwtClaim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

            $signatureInput = $jwtHeader . '.' . $jwtClaim;
            
            // Get private key
            $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
            if (!$privateKey) {
                throw new Exception("Invalid private key format");
            }
            
            openssl_sign($signatureInput, $signature, $privateKey, 'sha256');
            $jwtSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            
            $jwt = $jwtHeader . '.' . $jwtClaim . '.' . $jwtSignature;

            // Get OAuth2 token from Google
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $serviceAccount['token_uri'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
                    "assertion" => $jwt
                ]),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/x-www-form-urlencoded"
                ],
                CURLOPT_TIMEOUT => 30,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                throw new Exception("cURL error: " . $error);
            }
            
            if ($httpCode !== 200) {
                throw new Exception("OAuth2 token request failed with HTTP code: " . $httpCode . ". Response: " . $response);
            }
            
            $tokenData = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON response from OAuth2 server");
            }
            
            if (!isset($tokenData['access_token'])) {
                throw new Exception("No access token in response: " . $response);
            }
            
            return $tokenData['access_token'];
            
        } catch (Exception $e) {
            Log::error('FirebaseHelper Error: ' . $e->getMessage());
            return null;
        }
    }
}