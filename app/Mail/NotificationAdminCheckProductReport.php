<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationAdminCheckProductReport extends Mailable
{
    use Queueable, SerializesModels;

    public $reporterFullName = null;
    public $reporterEmail = null;
    public $reporterTel = null;
    public $productId = null;
    public $productOwnerId = null;
    public $productTitle = null;
    public $productPostedAt = null;
    public $reportType = null;
    public $description = null;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $reporterFullName,
        $reporterEmail,
        $reporterTel,
        $productId,
        $productOwnerId,
        $productTitle,
        $productPostedAt,
        $reportType,
        $description
    ) {
        $this->reporterFullName = $reporterFullName;
        $this->reporterEmail = $reporterEmail;
        $this->reporterTel = $reporterTel;
        $this->productId = $productId;
        $this->productOwnerId = $productOwnerId;
        $this->productTitle = $productTitle;
        $this->productPostedAt = $productPostedAt;
        $this->reportType = $reportType;
        $this->description = $description;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Báo cáo bài đăng | ' . env('APP_NAME'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.noti-admin-check-product-report',
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
