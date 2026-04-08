<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationController extends Controller
{
    /**
     * Show notification dashboard
     */
    public function index()
    {
        // Get users who have an FCM token for the dropdown
        $users = \App\Models\User::whereNotNull('fcm_token')
            ->select('id', 'user_name')
            ->get();

        return view('backend.notifications.index', compact('users'));
    }

    /**
     * Send custom notification
     */
    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:255',
            'target_type' => 'required|in:all,single',
            'user_id' => 'required_if:target_type,single|exists:users,id',
            'image_url' => 'nullable|url'
        ]);

        $serviceAccountPath = config('firebase.credentials');
        if (!file_exists($serviceAccountPath)) {
            return back()->with('error', 'Firebase credentials not found');
        }

        try {
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();

            $tokens = [];
            if ($request->target_type === 'all') {
                $tokens = \App\Models\User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
            } else {
                $user = \App\Models\User::find($request->user_id);
                if ($user && $user->fcm_token) {
                    $tokens = [$user->fcm_token];
                }
            }

            if (empty($tokens)) {
                return back()->with('error', 'No recipients found with valid device tokens');
            }

            $count = 0;
            $chunks = array_chunk($tokens, 500); // FCM limit for multicast is 500

            foreach ($chunks as $chunk) {
                foreach ($chunk as $token) {
                    $messageData = [
                        'token' => $token,
                        'notification' => [
                            'title' => $request->title,
                            'body'  => $request->body
                        ],
                        'data' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'type' => 'custom'
                        ]
                    ];

                    if ($request->image_url) {
                        $messageData['notification']['image'] = $request->image_url;
                    }

                    $message = CloudMessage::fromArray($messageData);
                    try {
                        $messaging->send($message);
                        $count++;
                    } catch (\Exception $e) {
                        \Log::warning("Failed to send notification to token: " . $token . " Error: " . $e->getMessage());
                    }
                }
            }

            return back()->with('success', "Notification sent successfully to {$count} devices!");

        } catch (\Exception $e) {
            \Log::error('Firebase Notification Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to send notification: ' . $e->getMessage());
        }
    }

    public function test(Request $request)
    {
        $token = $request->token;

        $serviceAccountPath = config('firebase.credentials');
        
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase credentials not found'], 500);
        }

        try {
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();

            $message = CloudMessage::fromArray([
                'token' => $token,
                'notification' => [
                    'title' => 'Test Notification',
                    'body'  => 'Firebase is working'
                ],
                'data' => [
                    'type' => 'test'
                ]
            ]);

            $messaging->send($message);
            return response()->json(['status' => 'sent']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}