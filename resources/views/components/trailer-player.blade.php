@php
    // Convert youtu.be/ID or youtube.com/watch?v=ID to embed URL
    $embedUrl = null;
    if ($url) {
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            $embedUrl = 'https://www.youtube-nocookie.com/embed/'.$m[1];
        } elseif (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            $embedUrl = 'https://www.youtube-nocookie.com/embed/'.$m[1];
        } elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            $embedUrl = 'https://www.youtube-nocookie.com/embed/'.$m[1];
        }
    }
@endphp

@if($embedUrl)
    <div class="relative w-full" style="padding-bottom:56.25%">
        <iframe
            src="{{ $embedUrl }}"
            title="Trailer"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
            loading="lazy"
            class="absolute inset-0 w-full h-full rounded-xl border-0">
        </iframe>
    </div>
@elseif($url)
    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
       class="inline-flex items-center gap-2 text-anime-600 dark:text-anime-400 hover:underline text-sm">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        {{ __('Watch Trailer') }}
    </a>
@endif
