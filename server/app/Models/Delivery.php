<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'deliveryman_id',
        'starting_point',
        'end_point',
        'status',
        'start_time',
        'end_time',
        'price',
        'document_id',
        "IIN",
        "receiver_IIN",
        "operator_id"
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the client associated with the delivery.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the deliveryman associated with the delivery.
     */
    public function deliveryman()
    {
        return $this->belongsTo(Deliveryman::class);
    }
}
