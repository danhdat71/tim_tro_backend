<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Throwable;
// use Google_Client;

trait NotificationTrait
{
    public function getMessaging()
    {
        return (new Factory)
            ->withServiceAccount(base_path().'/config/google/config.json')
            ->createMessaging();
    }

    // function getAccessToken()
    // {
    //     $client = new Google_Client();
    //     $client->setAuthConfig(base_path().'/config/google/config.json');
    //     $client->addScope($this->firebaseMessagingScope);
    //     $client->refreshTokenWithAssertion();
    //     $token = $client->getAccessToken();
    //     $result = $token['access_token'];
                
    //     return $result;
    // }

    public function formatDeviceTokens($input)
    {
        $trimmedInput = trim($input, '[]');
        $tokens = explode(',', $trimmedInput);
        $formattedOutput = "[\n";
        foreach ($tokens as $token) {
            $formattedOutput .= "  " . trim($token) . ",\n";
        }
        $formattedOutput = rtrim($formattedOutput, ",\n") . "\n]";
    
        return $formattedOutput;
    }

    /**
     * $token = ['device_token_1', 'device_token_2']
     * $notificationData = [
     *  'title' => 'your title',
     *  'body' => 'your notification body',
     *  'img' => 'Your image'
     * ]
     * **/
    function sendNotificationToMultiple($deviceTokens, $notificationData)
    {
        try {
            $message = CloudMessage::new()
                ->withNotification($notificationData);
            $result = $this->getMessaging()->sendMulticast($message, $deviceTokens);
            Log::channel('fcm_notification')
                ->info(
                    'Sent ' . json_encode($notificationData, JSON_UNESCAPED_UNICODE) . " to tokens: \n" . $this->formatDeviceTokens(implode(',', $deviceTokens)) .
                    "\nWith [Success: {$result->successes()->count()}, Failed: {$result->failures()->count()}]\n"
                );

            return true;
        } catch (Throwable $th) {
            Log::channel('fcm_notification')->info(
                'Sent failed ' . json_encode($notificationData, JSON_UNESCAPED_UNICODE) . 'to ' . implode(',', $deviceTokens) . "with detail error: \n"
                . $th->getMessage()
            );

            return false;
        }
    }
}
