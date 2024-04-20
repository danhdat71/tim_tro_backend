<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    public $table = 'wards';

    public function district()
    {
        return $this->belongsTo(Ward::class, 'district_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'ward_id', 'id');
    }
}
