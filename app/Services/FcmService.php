<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FcmService
{
    private const FCM_SCOPE    = 'https://www.googleapis.com/auth/firebase.messaging';
    private const TOKEN_CACHE  = 'fcm_oauth_token';
    private const TOKEN_TTL    = 3300; // 55 min (token expires in 60 min)

    /**
     * Get a short-lived OAuth2 access token from the service account JSON.
     * Token is cached for ~55 minutes to avoid redundant requests.
     */
    private static function getAccessToken(): ?string
    {
        if (Cache::has(self::TOKEN_CACHE)) {
            return Cache::get(self::TOKEN_CACHE);
        }

        $keyPath = config('services.fcm.service_account_path');

        if (!$keyPath || !file_exists($keyPath)) {
            Log::error('FCM: service_account.json not found at path: ' . $keyPath);
            return null;
        }

        try {
            $credentials = new ServiceAccountCredentials(
                self::FCM_SCOPE,
                $keyPath
            );

            $token = $credentials->fetchAuthToken();
            $accessToken = $token['access_token'] ?? null;

            if ($accessToken) {
                Cache::put(self::TOKEN_CACHE, $accessToken, self::TOKEN_TTL);
            }

            return $accessToken;
        } catch (\Exception $e) {
            Log::error('FCM: Failed to fetch OAuth token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send a chat push notification via FCM HTTP v1 API.
     *
     * @param  string $fcmToken   Receiver's FCM device token
     * @param  string $title      Notification title (sender name)
     * @param  string $body       Notification body (message text)
     * @param  array  $data       Extra data payload (type, chat_id, sender_id, etc.)
     * @return bool
     */
    public static function sendPush(
        string $fcmToken,
        string $title,
        string $body,
        array  $data = []
    ): bool {
        $accessToken = self::getAccessToken();
        if (!$accessToken) return false;

        $projectId = config('services.fcm.project_id');
        if (!$projectId) {
            Log::error('FCM: project_id not configured in services.fcm');
            return false;
        }

        $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // FCM HTTP v1 payload
        $payload = [
            'message' => [
                'token'        => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                // data values must all be strings
                'data'         => collect($data)->map(fn($v) => (string) $v)->toArray(),
                'android'      => [
                    'priority'     => 'high',
                    'notification' => [
                        'sound'        => 'default',
                        'channel_id'   => 'high_importance_channel',
                    ],
                ],
                'apns'         => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info('FCM: Push sent successfully', ['token_prefix' => substr($fcmToken, 0, 20)]);
                return true;
            }

            Log::warning('FCM: Push failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            // If token is invalid/expired, clear it from cache so next call re-fetches
            if ($response->status() === 401) {
                Cache::forget(self::TOKEN_CACHE);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('FCM: HTTP request failed: ' . $e->getMessage());
            return false;
        }
    }
}
