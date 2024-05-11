<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Jobs\SendMailJob;

class SendMailService
{
    public static $queueConnection = null; // Queue connection sync, database,...

    public function __construct()
    {
        self::$queueConnection = config('queue.default');
    }

    public static function queueConnection($queueConnection)
    {
        self::$queueConnection = $queueConnection;
        return new self;
    }

    public static function sendMail(
        $to,
        $mailableTemplate,
        ...$mailData
    ) {
        $to = (array) $to;

        // Check send to is empty
        if (empty($to)) {
            return;
        }

        // Log start send
        Log::channel('send_mail')
            ->info("Starting to send $mailableTemplate to " . implode(',', $to));

        // Dispatch send
        $queueConnection = self::$queueConnection ?? config('queue.default');
        dispatch(new SendMailJob($to, $mailableTemplate, ...$mailData))
            ->onConnection($queueConnection);

        return true;
    }
}
