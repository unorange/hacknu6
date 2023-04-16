<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleCodeShipped extends Model
{
    use HasFactory;

    protected $table = "single_code_shippeds";

    protected $fillable = [
        "delivery_id",
        "code",
    ];
}

