<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(private Review $review) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'review_approved',
            'message' => 'Your review for "' . $this->review->anime?->title . '" has been approved.',
            'url'     => '/anime/' . ($this->review->anime?->slug ?? $this->review->anime_id),
        ];
    }
}
