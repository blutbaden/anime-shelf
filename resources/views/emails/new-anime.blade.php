<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .header { background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff; padding: 32px 40px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .header p { margin: 8px 0 0; opacity: .85; font-size: 14px; }
        .body { padding: 40px; }
        .anime-card { display: flex; gap: 16px; background: #f9fafb; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
        .anime-card img { width: 80px; height: 112px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
        .anime-info h2 { margin: 0 0 8px; font-size: 18px; color: #111; }
        .anime-info p { margin: 0 0 4px; font-size: 13px; color: #6b7280; }
        .synopsis { font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 24px; }
        .btn { display: inline-block; padding: 12px 28px; background: #7c3aed; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px; }
        .footer { text-align: center; padding: 24px 40px; border-top: 1px solid #f3f4f6; font-size: 12px; color: #9ca3af; }
        .footer a { color: #7c3aed; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🎌 New Anime Added!</h1>
        <p>AnimeShelf • New Release</p>
    </div>
    <div class="body">
        <div class="anime-card">
            @if($anime->photo)
                <img src="{{ asset('storage/'.$anime->photo->file) }}" alt="{{ $anime->title }}">
            @endif
            <div class="anime-info">
                <h2>{{ $anime->title }}</h2>
                @if($anime->title_japanese)<p>{{ $anime->title_japanese }}</p>@endif
                <p>{{ $anime->type }} · {{ $anime->episodes ?? '?' }} episodes</p>
                <p>{{ $anime->season }} {{ $anime->season_year }}</p>
                @if($anime->studio)<p>{{ $anime->studio->name }}</p>@endif
            </div>
        </div>

        @if($anime->synopsis)
            <p class="synopsis">{{ Str::limit($anime->synopsis, 300) }}</p>
        @endif

        <a href="{{ route('anime', $anime->id) }}" class="btn">View Anime →</a>
    </div>
    <div class="footer">
        <p>You're receiving this because you subscribed to AnimeShelf updates.</p>
        <p><a href="{{ route('unsubscribe', ['token' => $token ?? '']) }}">Unsubscribe</a></p>
    </div>
</div>
</body>
</html>
