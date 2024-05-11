<?php

namespace App\Jobs;

use App\Mail\NotiListNewProductsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotiListNewProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subject = '';
    public $user;
    public $listNewProducts;

    /**
     * Create a new job instance.
     */
    public function __construct($subject, $user, $listNewProducts)
    {
        $this->subject = $subject;
        $this->user = $user;
        $this->listNewProducts = $listNewProducts;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new NotiListNewProductsMail($this->subject, $this->user, $this->listNewProducts));
    }
}
