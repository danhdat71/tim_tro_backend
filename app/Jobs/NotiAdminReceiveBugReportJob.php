<?php

namespace App\Jobs;

use App\Mail\NotiAdminReceiveBugReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotiAdminReceiveBugReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bugData;

    /**
     * Create a new job instance.
     */
    public function __construct($bugData)
    {
        $this->bugData = $bugData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to(env('ADMIN_EMAIL'))
            ->send(new NotiAdminReceiveBugReportMail($this->bugData));
    }
}
