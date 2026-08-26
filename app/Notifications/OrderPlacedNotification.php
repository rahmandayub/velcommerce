<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Pesanan #{$this->order->order_number} berhasil dibuat")
            ->greeting("Hai {$notifiable->name},")
            ->line("Terima kasih telah berbelanja. Pesanan #{$this->order->order_number} telah kami terima.")
            ->line('Total pembayaran: Rp '.number_format((float) $this->order->total, 0, ',', '.'))
            ->action('Lihat Pesanan', route('orders.show', ['order' => $this->order->order_number]))
            ->line('Kami akan menginformasikan saat pembayaran dikonfirmasi.');
    }
}
