@php
    $videoId  = null;
    $watchUrl = null;
    if ($url) {
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            $videoId = $m[1];
        } elseif (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            $videoId = $m[1];
        } elseif (preg_match('/youtube(?:-nocookie)?\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            $videoId = $m[1];
        }
        if ($videoId) {
            $watchUrl = 'https://www.youtube.com/watch?v=' . $videoId;
            // Clean embed URL — no enablejsapi, no autoplay, no origin issues
            $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $videoId . '?rel=0';
        }
    }
@endphp

@if($videoId)
    {{-- Alpine listens for YouTube's postMessage errors and swaps to a fallback link --}}
    <div x-data="{ blocked: false }"
         x-init="
             window.addEventListener('message', e => {
                 try {
                     const d = JSON.parse(e.data);
                     if (d.event === 'infoDelivery' && d.info && d.info.playerState === 5) return;
                     if ((d.event === 'onError' || d.info?.error) && [100,101,150,153].includes(d.info?.error ?? d.info)) {
                         blocked = true;
                     }
                 } catch {}
             });
         ">
        <div x-show="!blocked" class="relative w-full" style="padding-bottom:56.25%">
            <iframe
                src="{{ $embedUrl }}"
                title="Trailer"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy"
                class="absolute inset-0 w-full h-full rounded-xl border-0">
            </iframe>
        </div>
        <a x-show="blocked"
           href="{{ $watchUrl }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/>
            </svg>
            {{ __('Watch on YouTube') }}
        </a>
    </div>
@elseif($url)
    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
       class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/>
        </svg>
        {{ __('Watch Trailer') }}
    </a>
@endif
