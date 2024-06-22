<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\DB;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, NotificationTrait;

    public $notificationData = [];
    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($notificationData, $userId)
    {
        $this->notificationData = $notificationData;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $deviceTokens = DB::table('user_fcm_tokens')
            ->select('fcm_token')
            ->where('user_id', $this->userId)
            ->pluck('fcm_token')
            ->toArray();
        $this->sendNotificationToMultiple($deviceTokens, $this->notificationData);
    }
}
