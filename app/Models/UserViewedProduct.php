<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserViewedProduct extends Model
{
    use HasFactory;

    public $table = 'user_viewed_product';

    public $fillable = [
        'product_id',
        'user_id',
        'guest_ip',
    ];
}
