<?php
namespace App\Services\Egov;

use GuzzleHttp\Client as Guzzle;

class UserInfoHandler
{
    public static function getUserInfo(string $IIN)
    {
        $client = new Guzzle();

        // print_r("Bearer ".TokenHandler::getToken());

        $res = $client->get("http://hakaton-fl.gov4c.kz/api/persons/".$IIN,[
            'headers' => [
                'Authorization' => "Bearer ".TokenHandler::getToken()->token
            ],
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public static function getUserPhoneInfo(string $IIN)
    {
        $client = new Guzzle();

        $res = $client->get("http://hakaton.gov4c.kz/api/bmg/check/".$IIN,[
            'headers' => [
                'Authorization' => "Bearer ".TokenHandler::getToken()->token
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }
}
