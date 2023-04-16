<?php

namespace App\Services\Egov;

use App\Models\EgovToken;
use GuzzleHttp\Client as Guzzle;

class TokenHandler
{
    public static function getToken()
    {
        if(EgovToken::count() < 1 )
        {
            $token = self::createToken();

        } else{
            $token = EgovToken::where("id",'>',0)->first();   
        }

        return $token = $token->checkToken() ? $token : self::updateToken();

    }

    private static function createToken()
    {
        $token = EgovToken::orderBy("id","ASC")->first();

        if($token){
            $token = $token->checkToken() ? $token : self::updateToken();
        } else{
            $token = EgovToken::create([
                "token" => self::receiveToken()
            ]);
        } 
        return $token;
    }

    private static function updateToken()
    {
        $token = EgovToken::orderBy("id","ASC")->first();
        
        if($token){
            $token->update([
                "token" => self::receiveToken()
            ]);
        } else{
            $token = self::createToken();
        }

        return $token;
    }

    private static function receiveToken()
    {
        $client = new Guzzle();

        $res = $client->post("http://hakaton-idp.gov4c.kz/auth/realms/con-web/protocol/openid-connect/token",[
            "Headers" => [
                "Content-Type" => "application/x-www-form-urlencoded'"
            ],
            "form_params" => [
                "username" => "test-operator",
                "password" => "DjrsmA9RMXRl",
                "client_id" => "cw-queue-service",
                "grant_type" => "password"
            ]
        ]);

        return json_decode($res->getBody()->getContents())->access_token;
    }
}