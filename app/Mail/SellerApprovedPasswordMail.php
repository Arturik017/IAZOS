<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerApprovedPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
        public string $shopName,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cont seller aprobat pe IAZOS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.seller_approved_password',
        );
    }
}
