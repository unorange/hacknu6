<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleCodeAuth extends Model
{
    use HasFactory;

    protected $fillable = [
        'expires',
        'code',
        'client_id',
    ];

    public $timestamps = false;

    /**
     * Get the client associated with the single code authentication.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
