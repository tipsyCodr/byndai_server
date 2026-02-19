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

    public function sendOtp($number, $message, $dltTemplateId = null, $peid = null)
    {
        // Ensure number has 91 prefix if it's 10 digits
        if (strlen($number) == 10) {
            $number = '91' . $number;
        }

        // Use default PEID if not provided
        if (empty($peid)) {
            $peid = config('services.saakshisms.default_peid');
        }

        try {
            $params = [
                'user' => $this->user,
                'password' => $this->password,
                'senderid' => $this->senderId,
                'channel' => 'Trans', // Changed to Trans for OTP
                'DCS' => 0,
                'flashsms' => 0,
                'number' => $number,
                'text' => $message,
                'route' => $this->route,
            ];

            if ($dltTemplateId) {
                $params['DLTTemplateId'] = $dltTemplateId;
            }

            if ($peid) {
                $params['PEID'] = $peid;
            }

            $response = Http::get($this->baseUrl, $params);

            Log::info('SMS Response: ' . $response->body());

            return $jsonResponse = $response->json();
        } catch (\Exception $e) {
            Log::error('SMS Send Failed: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkDelivery($jobId)
    {
        try {
            $response = Http::get('http://bulksms.saakshisoftware.com/api/mt/GetDelivery', [
                'user' => $this->user,
                'password' => $this->password,
                'jobid' => $jobId,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SMS Delivery Check Failed: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
