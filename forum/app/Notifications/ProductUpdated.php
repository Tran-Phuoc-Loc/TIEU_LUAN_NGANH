<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductUpdated extends Notification
{
    use Queueable;
    private $product;

    /**
     * Create a new notification instance.
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Sản phẩm của bạn đã được cập nhật')
                    ->line("Sản phẩm '{$this->product->name}' của bạn đã được admin chỉnh sửa.")
                    ->action('Xem sản phẩm', url("/products/{$this->product->id}"))
                    ->line('Cảm ơn bạn đã sử dụng hệ thống!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
