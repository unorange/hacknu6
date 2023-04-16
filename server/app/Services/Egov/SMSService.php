<?php

namespace App\Services\Egov;

use Exception;
use GuzzleHttp\Client as Guzzle;

class SMSService
{
    public static function sendByPhone(string $phone, string $text)
    {
        $client = new Guzzle();

        $res = $client->post("http://hak-sms123.gov4c.kz/api/smsgateway/send",[
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer ".TokenHandler::getToken()->token
            ],
            'body' => json_encode([
                'phone' => $phone,
                'smsText' => $text
            ])
        ]);
    }

    public static function sendByIIN(string $IIN, string $text)
    {
        $client = new Guzzle();

        $IIN = strval($IIN);
        $userPhoneInfo = UserInfoHandler::getUserPhoneInfo($IIN);
        if($userPhoneInfo->isExists)
        {
            $res = $client->post("http://hak-sms123.gov4c.kz/api/smsgateway/send",[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer ".TokenHandler::getToken()->token
                ],
                'body' => json_encode([
                    'phone' => $userPhoneInfo->phone,
                    'smsText' => $text
                ])
            ]);
        } else{
            throw new Exception("not found",404);
        }

        
    }
}