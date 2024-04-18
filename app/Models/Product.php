<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FullTextSearch;

class Product extends Model
{
    use HasFactory;
    use FullTextSearch;
    
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

    protected $searchable = [
        'title',
        'description',
        'detail_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
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
