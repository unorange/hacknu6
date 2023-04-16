<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the regions for the district.
     */
    public function regions()
    {
        return $this->hasMany(Region::class);
    }
    
}
