<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        return $this->subject('Plata confirmată ✅ (#' . ($this->order->order_number ?? $this->order->id) . ')')
            ->view('emails.payment_confirmed');
    }
}
