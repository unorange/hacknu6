<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DeliverymanAuthController;
use App\Http\Controllers\DeliverymanController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SmsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function(){
    // Route::middleware("check_client")->group(function(){
        Route::get("/locations",[LocationController::class,"index"]);
        Route::get("/cons/{locationId}",[ConController::class,"getFromRegionId"]);
        Route::get("/delivery-services/{locationId}",[DeliveryController::class,"getByRegionId"]);
        
        Route::post("/calculate-price",[DeliveryController::class,"calculatePrice"]);
        Route::post("/create-delivery",[DeliveryController::class,"create"]);
    
    // });

    // Route::middleware("check_deliveryman")->group(function(){
        // Route::get('/deliveryman/profile', function (Request $request) {
        //     return $request->user();
        // });
    // });
    
});

Route::get("/check-document/{documentId}",[DeliveryController::class,"checkDocument"]);

Route::post('/single-code-auth/generate/{IIN}', [SmsController::class, 'sendCode']);
Route::post('/single-code-auth/login', [SmsController::class, 'auth']);

// Route::post('/deliveryman/register', [DeliverymanAuthController::class, 'register']);
// Route::post('/deliveryman/login', [DeliverymanAuthController::class, 'login']);

// НЕ УСПЕЛИ СДЕЛАТЬ АВТОРИЗАЦИЮ
Route::get("/deliveryman/auth/{phone}",[DeliverymanAuthController::class,"get"]);
Route::post("/deliveryman/change-status/{phone}",[DeliverymanController::class,"changeStatus"]);
Route::get('/deliveryman/check-status/{phone}',[DeliverymanController::class,"checkOwnStatus"]);
Route::post('/deliveryman/update-delivery-status/',[DeliverymanController::class,"updateDeliveryStatus"]);
Route::post('/deliveryman/input-order-code/',[DeliverymanController::class,"inputOrderCode"]);
Route::get('/deliveryman/get-history/{phone}',[DeliverymanController::class,"getHistory"]);

