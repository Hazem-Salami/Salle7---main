<?php

namespace App\Http\Traits;

use App\Models\User;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\Exceptions\InvalidOptionsException;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;


trait NotificationsTrait
{
    public function notify1($title, $body)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
//        $FcmToken = User::whereNotNull('device_key')->pluck('device_key')->all();

        $serverKey = env('fcm_server_key_2');

        $data = [
            "registration_ids" => User::find(3)->fcm_token,
            "notification" => [
                "title" => $title,
                "body" => $body,
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
    }

    public function notify2($title, $body, $data, $app, $id = null){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['key1' => 'value1', 'key2' => 'value2']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $user = User::find($id);
        $token = $user->fcm_token;
        if ($user->fcm_token != null)
        LaravelFcm::to([$token])
            ->options($option)
            ->notification($notification)
            ->data($data)
            ->send();
    }

    public function notify($title, $body, $data, $app, $id = null): string
    {
        $optionBuilder = new OptionsBuilder();
        try {
            $optionBuilder->setTimeToLive(60 * 20);
        } catch (InvalidOptionsException $e) {
            return response([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 500);
        }

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body);
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => $data]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

//        if ($app == 1)
//            config([
//                'fcm.api_key' => env('FCM_SERVER_KEY_1'),
//                'fcm.sender_id' => env('FCM_SENDER_ID_1'),
//            ]);
//        else
//        $serverKey = "AAAAUd4LQJs:APA91bFrRMqG0xEmJXMbI8PFUc8FTeG8Md2Ut4a1j6vccLSVlbfn8J8JufQyRalUJDMITs0Gy6Ftpo2VGyntSGPpUPeKVLLSHZykcfgLftjAh5kIFHfb77RWc3Sw3ilRmP2GgXr0XymD";
//        $senderId = '351617630363';
//        FCM::setApiKey($serverKey);
//        FCM::setSenderId($senderId);

        if ($id == null) {
            $users = User::all();
            foreach ($users as $user) {
                if ($user->fcm_token != null)
                    FCM::sendTo($user->fcm_token, $option, $notification, $data);
            }
        } else {
            $user = User::find($id);
            $token = $user->fcm_token;
            if ($user->fcm_token != null)
                FCM::sendTo($token, $option, $notification, $data);
        }
        return "success";
    }
}
