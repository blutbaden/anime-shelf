<?php

namespace App\Notifications;

use App\Models\Anime;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAnimeNotification extends Notification
{
    use Queueable;

    public function __construct(private Anime $anime) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'new_anime',
            'anime_id'=> $this->anime->id,
            'title'   => $this->anime->title,
            'message' => 'New anime added: ' . $this->anime->title,
            'url'     => '/anime/' . ($this->anime->slug ?? $this->anime->id),
        ];
    }
}
