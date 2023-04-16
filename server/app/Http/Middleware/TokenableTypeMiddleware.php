<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class TokenableTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

     public function handle(Request $request, Closure $next)
     {
         // Get the bearer token from the request header
         $bearerToken = $request->bearerToken();
     
         if ($bearerToken) {
             // Find the token in the database
             $token = PersonalAccessToken::where('token', hash('sha256', $bearerToken))->first();
     
             if ($token) {
                 // Get the tokenable_type from the token
                 $tokenableType = $token->tokenable_type;
     
                 // Attach the tokenable_type to the request for further use
                 $request->attributes->set('tokenable_type', $tokenableType);
             }
         }
     
         return $next($request);
     }
}
