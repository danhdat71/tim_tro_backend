<?php

namespace App\Jobs;

use App\Mail\ProductPolicyNgMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotiProductPolicyNgJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $blockedProduct;
    public $authorProduct;

    /**
     * Create a new job instance.
     */
    public function __construct($blockedProduct, $authorProduct)
    {
        $this->blockedProduct = $blockedProduct;
        $this->authorProduct = $authorProduct;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->authorProduct->email)
            ->send(new ProductPolicyNgMail($this->authorProduct, $this->blockedProduct));
    }
}
