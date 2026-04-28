<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewReplyNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Review $parent,
        private Review $reply,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'review_reply',
            'message' => ($this->reply->user?->name ?? 'Someone') . ' replied to your review on "' . $this->parent->anime?->title . '".',
            'url'     => '/anime/' . ($this->parent->anime?->slug ?? $this->parent->anime_id),
        ];
    }
}
