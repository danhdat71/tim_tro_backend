<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    public $table = 'follows';
    public $fillable = [
        'follower_id',
        'follower_receive_id',
    ];
}
