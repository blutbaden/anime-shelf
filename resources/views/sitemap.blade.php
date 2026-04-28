<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/anime') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/genres') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ url('/studios') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ url('/contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>

    @foreach($animes as $anime)
    <url>
        <loc>{{ url('/anime/'.$anime->id) }}</loc>
        <lastmod>{{ $anime->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    @foreach($studios as $studio)
    <url>
        <loc>{{ url('/studio/'.$studio->id) }}</loc>
        <lastmod>{{ $studio->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

</urlset>
