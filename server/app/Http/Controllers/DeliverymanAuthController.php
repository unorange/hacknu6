<?php

namespace App\Http\Controllers;

use App\Models\Deliveryman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeliverymanAuthController extends Controller
{
    public function get(string $phone)
    {
        $deliveryman = Deliveryman::where("phone",$phone)->first();
        if($deliveryman){
            return response()->json($deliveryman);
        } else{
            return response()->json(401);
        }

    }

    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string|unique:deliverymans,phone',
            'password' => 'required|string|confirmed',
        ]);

        // Create a new deliveryman
        $deliveryman = new Deliveryman([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'delivery_service_id' => $request->delivery_service_id
        ]);
        $deliveryman->save();

        // Generate a token for the new deliveryman
        $token = $deliveryman->createToken('deliveryman-token')->plainTextToken;

        // Return the token to the user
        return response()->json([
            'token' => $token,
            'deliveryman' => $deliveryman,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $deliveryman = Deliveryman::where('phone', $request->phone)->first();

        if (!$deliveryman || !Hash::check($request->password, $deliveryman->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $deliveryman->createToken('deliveryman-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'deliveryman' => $deliveryman,
        ]);
    }
}
