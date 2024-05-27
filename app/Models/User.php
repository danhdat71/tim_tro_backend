<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'avatar',
        'app_id',
        'tel',
        'gender',
        'user_type',
        'birthday',
        'description',
        'last_login_at',
        'remember_token',
        'status',
        'leave_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function userSavedProducts()
    {
        return $this->belongsToMany(Product::class, 'users_saved_products', 'user_id', 'product_id')->withTimestamps();
    }

    public function userViewedProduct()
    {
        return $this->belongsToMany(Product::class, 'users_viewed_products', 'user_id', 'product_id')
            ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_receive_id', 'follower_id');
    }

    public function follow()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'follower_receive_id')->withTimestamps();
    }
}
