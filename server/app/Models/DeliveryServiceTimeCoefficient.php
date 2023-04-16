<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryServiceTimeCoefficient extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_service_id',
        'start_time',
        'end_time',
        'coefficient',
    ];
}

