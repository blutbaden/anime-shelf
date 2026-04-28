<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .header { background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff; padding: 32px 40px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 800; }
        .body { padding: 40px; font-size: 15px; color: #374151; line-height: 1.7; }
        .footer { text-align: center; padding: 24px 40px; border-top: 1px solid #f3f4f6; font-size: 12px; color: #9ca3af; }
        .footer a { color: #7c3aed; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🎌 AnimeShelf Newsletter</h1>
    </div>
    <div class="body">
        {!! strip_tags($content ?? '', '<p><br><b><strong><i><em><u><a><ul><ol><li><h1><h2><h3><h4><img><span><div>') !!}
    </div>
    <div class="footer">
        <p>You're receiving this because you subscribed to AnimeShelf.</p>
        <p><a href="{{ route('unsubscribe', ['token' => $token ?? '']) }}">Unsubscribe</a></p>
    </div>
</div>
</body>
</html>
