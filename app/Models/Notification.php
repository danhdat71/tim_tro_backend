<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'title',
        'description',
        'status',
        'link',
        'user_id',
        'sent_at',
    ];

    protected $appends = ['sent_at_ago'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSentAtAgoAttribute()
    {
        $carbon = Carbon::parse($this->sent_at);
        return $carbon->diffForHumans();
    }
}
