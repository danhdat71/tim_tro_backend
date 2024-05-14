<?php

namespace App\Jobs;

use App\Mail\NotiReporterBugWasFixedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotiReporterBugWasFixedJob implements ShouldQueue
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
        Mail::to($this->bugData->email)
            ->send(new NotiReporterBugWasFixedMail($this->bugData));
    }
}
