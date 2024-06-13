<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsAccess extends Model
{
    use HasFactory;

    protected $table = 'ads_access';

    protected $fillable = [
        'ads_id',
        'user_id',
        'guest_ip',
    ];
}
