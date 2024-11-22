<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductDeleted extends Notification
{
    use Queueable;
    private $productName;

    /**
     * Create a new notification instance.
     */
    public function __construct($productName)
    {
        $this->productName = $productName;
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
                    ->subject('Sản phẩm của bạn đã bị xóa')
                    ->line("Sản phẩm '{$this->productName}' của bạn đã bị admin xóa.")
                    ->line('Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ hỗ trợ.');
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
