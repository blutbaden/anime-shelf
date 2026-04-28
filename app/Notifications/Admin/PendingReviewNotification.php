<?php

namespace App\Notifications\Admin;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendingReviewNotification extends Notification
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
            'type'    => 'pending_review',
            'message' => 'New review pending approval for "' . $this->review->anime?->title . '".',
            'url'     => '/admin/reviews',
        ];
    }
}
