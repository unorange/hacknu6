<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);

        // Create a new user
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->save();

        // Generate a token for the new user
        $token = $user->createToken('API Token', expiresAt:now()->addHours(6))->plainTextToken;

        // Return the token to the user
        return response()->json([
            'token' => $token,
            "user" => $user
        ]);
    }
}
