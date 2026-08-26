<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order $order,
        public readonly OrderStatus $status,
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
        $map = [
            OrderStatus::Shipped => [
                'subject' => 'Pesanan Anda telah dikirim',
                'line' => 'Pesanan Anda telah dikirim dan dalam perjalanan.',
            ],
            OrderStatus::Completed => [
                'subject' => 'Pesanan telah selesai',
                'line' => 'Terima kasih! Pesanan Anda telah selesai. Selamat menikmati produk Anda.',
            ],
            OrderStatus::Cancelled => [
                'subject' => 'Pesanan dibatalkan',
                'line' => 'Pesanan Anda telah dibatalkan dan stok telah dikembalikan.',
            ],
        ];

        $config = $map[$this->status] ?? [
            'subject' => 'Status pesanan diperbarui',
            'line' => 'Status pesanan Anda telah diperbarui.',
        ];

        return (new MailMessage)
            ->subject($config['subject']." (#{$this->order->order_number})")
            ->greeting("Hai {$notifiable->name},")
            ->line($config['line'])
            ->action('Lihat Pesanan', route('orders.show', ['order' => $this->order->order_number]));
    }
}
