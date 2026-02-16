<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        return $this
            ->subject('Comanda ta a fost plasată (#' . ($this->order->order_number ?? $this->order->id) . ')')
            ->view('emails.order_placed');
    }
}
