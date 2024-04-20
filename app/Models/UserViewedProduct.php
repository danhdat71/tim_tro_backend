<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserViewedProduct extends Model
{
    use HasFactory;

    public $table = 'users_viewed_products';

    public $fillable = [
        'product_id',
        'user_id',
        'guest_ip',
    ];
}
