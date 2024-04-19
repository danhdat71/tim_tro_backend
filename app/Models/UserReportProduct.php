<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReportProduct extends Model
{
    use HasFactory;

    public $table = 'users_report_products';

    public $fillable = [
        'user_id',
        'product_id',
        'full_name',
        'email',
        'tel',
        'is_read',
        'report_type',
        'description',
    ];
}
