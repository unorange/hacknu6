<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'IIN',
        'last_name',
        'first_name',
        'middle_name',
        'eng_first_name',
        'eng_surname',
        'date_of_birth',
    ];

    public function singleCodeAuths()
    {
        return $this->hasMany(SingleCodeAuth::class);
    }
    

}
