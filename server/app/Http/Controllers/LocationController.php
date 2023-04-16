<?php

namespace App\Http\Controllers;

use App\Http\Resources\DistrictResource;
use App\Models\District;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $districts = District::with('regions')->get();
        return DistrictResource::collection($districts);
    }
}
