<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to = [];
    protected $mailData = null;
    protected $mailableTemplate = null;

    /**
     * Create a new job instance.
     */
    public function __construct($to, $mailableTemplate, ...$mailData)
    {
        $this->to = $to;
        $this->mailData = $mailData;
        $this->mailableTemplate = $mailableTemplate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $to = $this->to;
        $mailData = $this->mailData;
        $mailableTemplate = $this->mailableTemplate;

        try {
            $mail = Mail::to($to);

            $mail->send(new $mailableTemplate(...$mailData));

            Log::channel('send_mail')
                ->info("Mail $mailableTemplate has been sent successfully to " . implode(',', $to));

        } catch (Throwable $th) {
            Log::channel('send_mail')
                ->error("Mail $mailableTemplate has failed to send to " . implode(',', $to) . " | " .$th->getMessage());
        }
    }
}
