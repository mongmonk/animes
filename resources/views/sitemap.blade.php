{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('directory') }}</loc>
        <priority>0.8</priority>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc>{{ route('popular') }}</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('new-releases') }}</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('genres.index') }}</loc>
        <priority>0.7</priority>
        <changefreq>monthly</changefreq>
    </url>

    @foreach($genres as $genre)
    <url>
        <loc>{{ route('genres.show', $genre->slug) }}</loc>
        <priority>0.6</priority>
        <changefreq>weekly</changefreq>
    </url>
    @endforeach

    @foreach($animes as $anime)
    <url>
        <loc>{{ route('anime.detail', $anime->slug) }}</loc>
        <lastmod>{{ $anime->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        <priority>0.8</priority>
        <changefreq>weekly</changefreq>
    </url>
    @endforeach

    @foreach($episodes as $episode)
    <url>
        <loc>{{ route('anime.watch', ['animeSlug' => $episode->anime->slug, 'episodeSlug' => str_replace($episode->anime->slug . '/episode/', '', $episode->slug)]) }}</loc>
        <lastmod>{{ $episode->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        <priority>0.6</priority>
        <changefreq>never</changefreq>
    </url>
    @endforeach
</urlset>