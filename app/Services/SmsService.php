<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $baseUrl;
    protected $user;
    protected $password;
    protected $senderId;
    protected $channel;
    protected $route;

    public function __construct()
    {
        $this->baseUrl = 'http://bulksms.saakshisoftware.com/api/mt/SendSMS';
        $this->user = config('services.saakshisms.user');
        $this->password = config('services.saakshisms.password');
        $this->senderId = config('services.saakshisms.sender_id');
        $this->channel = config('services.saakshisms.channel', 'Promo');
        $this->route = config('services.saakshisms.route', '##');
    }

    public function sendOtp($number, $message)
    {
        // Ensure number has 91 prefix if it's 10 digits
        if (strlen($number) == 10) {
            $number = '91' . $number;
        }

        try {
            $response = Http::get($this->baseUrl, [
                'user' => $this->user,
                'password' => $this->password,
                'senderid' => $this->senderId,
                'channel' => $this->channel,
                'DCS' => 0,
                'flashsms' => 0,
                'number' => $number,
                'text' => $message,
                'route' => $this->route,
            ]);

            Log::info('SMS Response: ' . $response->body());

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SMS Send Failed: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
