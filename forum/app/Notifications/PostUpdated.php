<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostUpdated extends Notification
{
    use Queueable;
    protected $post;

    /**
     * Create a new notification instance.
     */
    public function __construct($post)
    {
        $this->post = $post;
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
            ->subject('Bài viết đã được cập nhật')
            ->line('Bài viết của bạn đã được cập nhật bởi admin.')
            ->action('Xem bài viết', route('posts.show', $this->post->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Admin đã chỉnh sửa bài viết của bạn: ' . $this->post->title,
            'post_id' => $this->post->id,
        ];
    }
}
