<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotiReporterBugWasFixedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bugData;
    public $isShowFooterHappy = true;

    /**
     * Create a new message instance.
     */
    public function __construct($bugData)
    {
        $this->bugData = $bugData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: env('APP_NAME') . ' | Phản hồi báo cáo lỗi',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.noti-reporter-bug-was-fixed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
