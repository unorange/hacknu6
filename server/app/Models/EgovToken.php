<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EgovToken extends Model
{
    use HasFactory;

    protected $fillable = [
        "token"
    ];

    public function checkToken()
    {
        $now = Carbon::now();
        $timeDifferenceInHours = $this->updated_at->diffInHours($now);

        return $timeDifferenceInHours < 1;
    }
}
