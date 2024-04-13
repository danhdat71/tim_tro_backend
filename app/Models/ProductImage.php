<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    public $table = 'product_images';

    public $fillable = [
        'product_id',
        'url',
        'thumb_url',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
