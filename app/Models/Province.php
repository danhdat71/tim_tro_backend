<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    public $table = 'provinces';

    public function districts()
    {
        return $this->hasMany(District::class, 'province_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'province_id', 'id');
    }
}
