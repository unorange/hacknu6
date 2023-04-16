<?php

namespace Database\Seeders;

use App\Models\CON;
use App\Models\Delivery;
use App\Models\Deliveryman;
use App\Models\DeliveryService;
use App\Models\District;
use App\Models\Operator;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RedionsAndDistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $d1 = District::create([
            "name" => "Астана"
        ]);
        $d2 = District::create([
            "name" => "Алматы"
        ]);
        $d3 = District::create([
            "name" => "Жамбылская область"
        ]);
        $d4 = District::create([
            "name" => "Павлодарская область"
        ]);

        Region::create([
            "district_id" => $d1->id,
            "name" => "Астана"
        ]);

        Region::create([
            "district_id" => $d2->id,
            "name" => "Алматы"
        ]);

        Region::create([
            "district_id" => $d3->id,
            "name" => "Шу"
        ]);

        Region::create([
            "district_id" => $d3->id,
            "name" => "Тараз"
        ]);

        Region::create([
            "district_id" => $d3->id,
            "name" => "Толе Би"
        ]);

        Region::create([
            "district_id" => $d4->id,
            "name" => "Павлодар"
        ]);

        Region::create([
            "district_id" => $d4->id,
            "name" => "Экибастуз"
        ]);

        $con = CON::create([
            "long" => 71.41,
            "lat" => 51.12,
            "address" => "улица Жанибек Керей Хандары 4",
            "region_id" => 1
        ]);

        $operator = Operator::create([
            "first_name" => "Yen",
            "last_name" => "Hegai",
            "con_id" => $con->id,
            "phone" => "77082845394"
        ]);

        DeliveryService::create([
            "name" => "DHL",
            "region_id" => 1,
            "base_coefficient" => 600,
            "image_url" => "https://hsto.org/getpro/moikrug/uploads/company/100/005/487/1/logo/medium_1cbdcd8d5c6a3a3bc3d0aa25afc8fcd5.jpg"
        ]);

        DeliveryService::create([
            "name" => "Kazpost",
            "region_id" => 1,
            "base_coefficient" => 160,
            "image_url" => "https://globalkz.biz/usr/catalog/big-catalog-15698195501.png"
        ]);

        DeliveryService::create([
            "name" => "Pony Express",
            "region_id" => 1,
            "base_coefficient" => 240,
            "image_url" => "https://ponyexpress.kz/local/templates/ponyexpress_main/i/logo.png"
        ]);

        Deliveryman::create([
            "last_name" => "Tyulkov",
            "first_name" => "Alexander",
            "status" => "active",
            "phone" => "77053843361",
            "delivery_service_id" => "1",
        ]);

        Deliveryman::create([
            "last_name" => "Sailaubek",
            "first_name" => "Nariman",
            "status" => "inactive",
            "phone" => "77082845394",
            "delivery_service_id" => "1",
        ]);
    }
    
    // 'first_name',
    // 'last_name',
    // 'phone',
    // 'status',
    // 'delivery_service_id',

}
