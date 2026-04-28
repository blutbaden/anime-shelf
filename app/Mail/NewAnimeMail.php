<?php

namespace App\Mail;

use App\Models\Anime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAnimeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Anime $anime,
        public string $unsubscribeUrl,
    ) {}

    public function build(): self
    {
        return $this->subject('New Anime Added: ' . $this->anime->title)
                    ->view('emails.new-anime');
    }
}
