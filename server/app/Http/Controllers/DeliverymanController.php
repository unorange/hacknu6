<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Deliveryman;
use App\Models\Operator;
use App\Models\SingleCodeShipped;
use App\Services\Egov\SMSService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeliverymanController extends Controller
{
    public function getHistory($phone){
        $deliveryman = Deliveryman::where("phone",$phone)->first();
        $deliveries = Delivery::where("status","delivery_id")->where("deliveryman_id",$deliveryman->id)->get();

        return $deliveries;
    }

    public function checkOwnStatus($phone)
    {
        $deliveryman = Deliveryman::where("phone",$phone)->first();

        if($deliveryman){
            if($deliveryman->status === "in_order"){
                $data = [
                    "status" => "in_order",
                    "delivery" => Delivery::where("deliveryman_id",$deliveryman->id)->first() 
                ];
            }else{
                $data = [
                    "status" => $deliveryman->status
                ];
            }

            return $data;
        }

        abort(401);
    }

    public function changeStatus($phone)
    {
        $deliveryman = Deliveryman::where("phone",$phone)->first();

        if($deliveryman){
            if($deliveryman->status === "in_order"){
                return response()->json(status:400);
            }

            if($deliveryman->status === "active"){
                $deliveryman->status = "inactive";
                $deliveryman->save();
                return response()->json("status changed to inactive");
            }

            if($deliveryman->status === "inactive"){
                $deliveryman->status = "active";
                $deliveryman->save();
                return response()->json("status changed to active");
            }

        } else{
            return response()->json(401);
        }
    }

    public function updateDeliveryStatus(Request $request){
        $delivery = Delivery::where("id",$request->delivery_id)->first();
        $deliveryman = Deliveryman::where("phone",$request->phone)->first();
        if($delivery->deliveryman_id !== $deliveryman->id)
        {
            abort(401);
        }

        if($delivery){
            if($delivery->status === "created"){
                $operator = Operator::where("id",$delivery->operator_id)->first();
                $code = rand(100000,999999);

                $text = "Shipped status confirmation code for operator : ".$code;

                SingleCodeShipped::create([
                    "delivery_id" => $delivery->id,
                    "code" => $code
                ]);

                SMSService::sendByPhone($operator->phone,$text);
            }

            if($delivery->status === "on_delivery"){
                $code = rand(100000,999999);

                $text = "Received status confirmation for receiver: ".$code;

                SingleCodeShipped::create([
                    "delivery_id" => $delivery->id,
                    "code" => $code
                ]);

                SMSService::sendByIIN(strval($delivery->receiver_IIN),$text);
            }
        }
    }

    public static function inputOrderCode(Request $request)
    {
        $code = $request->code;
        $delivery = Delivery::where("id",$request->delivery_id)->first();
        $deliveryman = Deliveryman::where("phone",$request->phone)->first();  
        if($deliveryman === null){
            abort(400);
        }
        $singleCode = SingleCodeShipped::where([
            ['delivery_id', '=', $request->delivery_id],
            ['code', '=', $code],
        ])->first();

        if($singleCode){
           $singleCode->delete();
            if($delivery->status === "created"){
                $delivery->status = "on_delivery";
                $delivery->save();
            }else if($delivery->status === "on_delivery"){
                $delivery->status = "delivery_id";
                $delivery->end_time = Carbon::now()->format('H:i:s');
                $delivery->save();

                $deliveryman->status = "active";
                $deliveryman->save();

            }

            return response()->json($delivery);
        }

        abort(400);


    }
}
// delivery_id"                "created",
// "on_delivery",
// "delivery_id"