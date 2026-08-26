<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  'paid'|'failed'  $outcome
     */
    public function __construct(
        public readonly Order $order,
        public readonly string $outcome,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->outcome === 'paid') {
            return (new MailMessage)
                ->subject("Pembayaran pesanan #{$this->order->order_number} berhasil")
                ->greeting("Hai {$notifiable->name},")
                ->line("Pembayaran untuk pesanan #{$this->order->order_number} telah kami terima.")
                ->line('Pesanan Anda sedang kami proses.')
                ->action('Lihat Pesanan', route('orders.show', ['order' => $this->order->order_number]));
        }

        return (new MailMessage)
            ->subject("Pembayaran pesanan #{$this->order->order_number} gagal")
            ->greeting("Hai {$notifiable->name},")
            ->line("Pembayaran untuk pesanan #{$this->order->order_number} tidak berhasil.")
            ->line('Silakan coba kembali dari halaman pesanan Anda.')
            ->action('Lihat Pesanan', route('orders.show', ['order' => $this->order->order_number]));
    }
}
