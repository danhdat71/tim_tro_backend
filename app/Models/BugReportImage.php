<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugReportImage extends Model
{
    use HasFactory;

    protected $table = 'bug_report_images';

    protected $fillable = [
        'bug_report_id',
        'url',
    ];
}
