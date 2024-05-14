<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    use HasFactory;

    protected $table = 'bug_reports';

    protected $fillable = [
        'full_name',
        'email',
        'description',
        'ip_address',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function bugReportImages()
    {
        return $this->hasMany(BugReportImage::class, 'bug_report_id', 'id');
    }
}
