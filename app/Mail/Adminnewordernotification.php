<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🛒 New Order #'.$this->order->order_number.' — $'.number_format((float) $this->order->total, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new-order-simple', // Using simple template for testing
            with: [
                'order'      => $this->order,
                'adminUrl'   => route('admin.orders.show', $this->order),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}