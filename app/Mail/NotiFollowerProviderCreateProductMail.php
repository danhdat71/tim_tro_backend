<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotiFollowerProviderCreateProductMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = '';
    public $finderName = '';
    public $product = null;
    public $provider = null;
    public $isShowFooterHappy = true;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $finderName, $product, $provider)
    {
        $this->subject = $subject;
        $this->finderName = $finderName;
        $this->product = $product;
        $this->provider = $provider;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: env('APP_NAME') . ' | ' . $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.noti-follower-provider-create-product',
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
