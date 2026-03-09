<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->order->status instanceof \BackedEnum
            ? ucfirst($this->order->status->value)
            : ucfirst($this->order->status);

        return new Envelope(
            subject: 'Your Order is '.$status.' — #'.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status-updated',
            with: [
                'order'    => $this->order,
                'orderUrl' => route('orders.show', $this->order),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}