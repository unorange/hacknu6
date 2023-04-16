<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Deliveryman extends Model
{
    use HasFactory;

    protected $table = "deliverymans";

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'status',
        'delivery_service_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function deliveryService()
    {
        return $this->belongsTo(DeliveryService::class);
    }
}
