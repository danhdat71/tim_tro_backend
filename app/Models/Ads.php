<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use HasFactory;

    protected $table = 'ads';
    protected $fillable = [
        'img_url',
        'organization',
        'link',
        'type',
        'status',
        'expired_at',
    ];

    public function adsAccess()
    {
        return $this->hasMany(AdsAccess::class, 'ads_id', 'id');
    }
}
