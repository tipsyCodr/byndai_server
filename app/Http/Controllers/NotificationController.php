<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationController extends Controller
{
    public function test(Request $request)
    {
        $token = $request->token;

        $serviceAccountPath = config('firebase.credentials');
        
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase credentials not found'], 500);
        }

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
    }
}