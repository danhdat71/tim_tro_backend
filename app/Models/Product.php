<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    public $table = 'products';

    public $fillable = [
        'user_id',
        'ward_id',
        'province_id',
        'district_id',
        'title',
        'slug',
        'price',
        'description',
        'tel',
        'detail_address',
        'lat',
        'long',
        'acreage',
        'bed_rooms',
        'toilet_rooms',
        'used_type',
        'is_shared_house',
        'time_rule',
        'is_allow_pet',
        'posted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function userViews()
    {
        return $this->hasMany(UserViewedProduct::class, 'product_id', 'id');
    }
}
