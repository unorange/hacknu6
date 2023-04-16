<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConResource;
use App\Models\CON;
use Illuminate\Http\Request;

class ConController extends Controller
{
    public function getFromRegionId(int $regionId)
    {
        $cons = CON::where('region_id', $regionId)->get();

        return ConResource::collection($cons);
    }
}
