<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CON extends Model
{
    use HasFactory;

    protected $table = 'cons';


    protected $fillable = [
        'long',
        'lat',
        'address',
        'region_id',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
