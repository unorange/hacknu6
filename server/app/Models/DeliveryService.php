<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region_id',
        'base_coefficient',
        "image_url"
    ];

        /**
     * Get the region associated with the delivery service.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the deliverymen for the delivery service.
     */
    public function deliveryman()
    {
        return $this->hasMany(Deliveryman::class);
    }
}
