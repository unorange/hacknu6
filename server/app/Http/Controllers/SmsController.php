<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Delivery;
use App\Models\Deliveryman;
use App\Models\SingleCodeAuth;
use App\Services\Egov\SMSService;
use App\Services\Egov\UserInfoHandler;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SmsController extends Controller
{
    public function sendShippedCode($request){
        $delivery_id = $request->delivery_id;
        $phone = $request->phone;

        $delivery = Delivery::where("id",$delivery_id)->get();
        $deliveryman = Deliveryman::where("phone",$phone)->first();
        
        if($delivery->deliveryman_id !== $deliveryman->id){
            return response()->json("Bad request",400);
        }

        



    }

    public function sendCode(string $IIN)
    {
        $randCode = rand(100000, 999999);
        $text = "Your single-use authentication code is ".$randCode;

        try {
            $clientInfo = UserInfoHandler::getUserInfo($IIN);
        } catch (\Throwable $th) {
            throw $th;
        }

        $client = Client::firstOrCreate([
            "IIN" => $IIN
        ], [
            "last_name" => $clientInfo->lastName,
            "first_name" => $clientInfo->firstName,
            "middle_name" => $clientInfo->middleName,
            "eng_first_name" => $clientInfo->engFirstName,
            "eng_surname" => $clientInfo->engSurname,
            "date_of_birth" => Carbon::parse($clientInfo->dateOfBirth),
        ]);

        // 'last_name',
        // 'first_name',
        // 'middle_name',
        // 'eng_first_name',
        // 'eng_surname',
        // 'date_of_birth',


        $singleCodeAuth = new SingleCodeAuth([
            'expires' => now()->addMinutes(10),
            'code' => $randCode,
            'client_id' => $client->id,
        ]);

        $singleCodeAuth->save();

        SMSService::sendByIIN($IIN,$text);

        return response()->json(status:201);

    }

    public function auth(Request $request)
    {
        $all = $request->json()->all();
        $IIN = $all["IIN"];
        $code = $all["code"];

        $client = Client::where("IIN",$IIN)->first();

        $singleCodeAuth = SingleCodeAuth::where([
            ['client_id', '=', $client->id],
            ['code', '=', $code],
        ])->first();

        if (!$singleCodeAuth) {
            return response()->json(['message' => 'Invalid code'], 401);
        }

        if ($singleCodeAuth->expires < now()) {
            return response()->json(['message' => 'Expired code'], 401);
        }

        $token = $client->createToken('SingleCodeAuthToken')->plainTextToken;

        // Delete the used code
        $singleCodeAuth->delete();

        // Return the token as a response
        return response()->json([
            'person' => $client,
            'token' => $token
        ]);

    }
}
