<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryServiceResource;
use App\Models\CON;
use App\Models\Delivery;
use App\Models\Deliveryman;
use App\Models\DeliveryService;
use App\Models\Operator;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    public function checkDocument($documentId)
    {
        $count = Delivery::where("document_id",$documentId)->count();
        return response()->json($count > 0 ? false : true);
    }

    public function getByRegionId(int $regionId)
    {
        $deliveryServices = DeliveryService::where("region_id", $regionId)->get();

        return DeliveryServiceResource::collection($deliveryServices);
    }

    public function calculatePrice(Request $request)
    {
        $address = $request->address;
        $con_id = $request->con_id;
        $delivery_service_id = $request->delivery_service_id;

        $tariff = DeliveryService::where("id",$delivery_service_id)->first()->base_coefficient;
        $c1 = $this->getLatLongFromAddress(CON::where("id",$con_id)->first()->address);
        $c2 = $this->getLatLongFromAddress($address);

        $distance = $this->haversine_distance($c1["lat"],$c1["long"],$c2['lat'],$c2['long']);

        return [
            "tariff" => $tariff,
            "distance" => $distance,
            'price' => intval($distance * $tariff)
        ];
    }

    public function create(Request $request)
    {
        $address = $request->address;
        $documentId = $request->document_id;
        $con_id = $request->con_id;
        $delivery_service_id = $request->delivery_service_id;
        $receiver_IIN = $request->receiver_IIN;


        $availableDeliverymans = Deliveryman::where("delivery_service_id",$delivery_service_id)->where("status","active")->get();

        if($availableDeliverymans->count() === 0){
            return response()->json("No available couriers", 503);
        }

        $deliveryman = $availableDeliverymans->random();

        $deliveryman->status = "in_order";
        $deliveryman->save();
        
        $tariff = DeliveryService::where("id",$delivery_service_id)->first()->base_coefficient;
        $c1 = $this->getLatLongFromAddress(CON::where("id",$con_id)->first()->address);
        $c2 = $this->getLatLongFromAddress($address);

        $distance = $this->haversine_distance($c1["lat"],$c1["long"],$c2['lat'],$c2['long']);

        $data =  [
            "tariff" => $tariff,
            "distance" => $distance,
            'price' => intval($distance * $tariff)
        ];

        $user = auth('sanctum')->user();

        try {
            $delivery = Delivery::create([
                "IIN" => $user->IIN,
                "client_id" => $user->id,
                "deliveryman_id" => $deliveryman->id,
                "starting_point" => CON::where("id",$con_id)->first()->address,
                "end_point" => $address,
                "status" => "created",
                "start_time" => Carbon::now()->format('H:i:s'),
                "document_id" => $documentId,
                "price" => $data["price"],
                "receiver_IIN" => $receiver_IIN,
                "operator_id" => Operator::all()->random()->id
            ]);
    
        } catch (\Throwable $th) {
            $deliveryman->status = "active";
            $deliveryman->save();

            throw $th;
        }

        $delivery->deliveryman = $deliveryman;

        return response($delivery,201);

        // "created",
        // "on_delivery",
        // "delivery_id"

        // 'client_id',
        // 'deliveryman_id',
        // 'starting_point',
        // 'end_point',
        // 'status',
        // 'start_time',
        // 'end_time',
        // 'price',
        // 'document_id'
    }

    private function getLatLongFromAddress(string $address)
    {
        $client = new Guzzle();
        $res = $client->get("https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates",[
            "query" => [
                "f" => "pjson",
                'SingleLine' => $address
            ]
        ]);

        $candidates = json_decode($res->getBody()->getContents(),1)["candidates"];

        return [
            "long" => $candidates[0]["location"]["x"],
            "lat" => $candidates[0]["location"]["y"],
        ];

    }

    private function haversine_distance($latitude1, $longitude1, $latitude2, $longitude2) {
        $earth_radius = 6371; // Earth's radius in kilometers
    
        $lat_delta = deg2rad($latitude2 - $latitude1);
        $lon_delta = deg2rad($longitude2 - $longitude1);
    
        $a = sin($lat_delta / 2) * sin($lat_delta / 2) +
             cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) *
             sin($lon_delta / 2) * sin($lon_delta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $earth_radius * $c;
    
        return $distance;
    }
}
