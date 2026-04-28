# Anime Shelf

A full-featured anime streaming and tracking platform built with Laravel 13, inspired by HiAnime and Crunchyroll.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13.6, PHP 8.3 |
| Database | PostgreSQL |
| Frontend | Tailwind CSS v3, Alpine.js |
| Asset Pipeline | Vite + laravel-vite-plugin |
| Auth | Laravel Fortify (email + Google + GitHub OAuth) |
| PDF export | barryvdh/laravel-dompdf |
| HTTP client | GuzzleHTTP |
| Image processing | Intervention Image |

---

## Features

### Public

- **Spotlight carousel** — hero section showing top anime with auto-rotation, fade transitions, arrows and dot indicators
- **Anime catalog** — filterable by type, status, genre, season; sortable; paginated
- **Anime detail page** — poster, synopsis, trailer (YouTube embed), metadata card (type, seasons, episodes, duration, status, season, rating, source, aired), genres, reviews with voting and replies
- **Episode watch page** — supports YouTube, Dailymotion, and direct video URLs; prev/next navigation; episode list sidebar
- **Genre browser** — grid of genre cards
- **Studio browser** — searchable grid; individual studio page with anime list
- **Watch list** — plan to watch, watching, completed, on hold, dropped
- **Watch history** and recently viewed
- **Episode progress tracker** — increment/decrement current episode with auto-save
- **Favorites**
- **Reviews and ratings** — 1–10 star rating, reply threads, upvote/downvote
- **Personalized recommendations** — based on top genres in watch history
- **Daily quote** — random anime quote shown on home page
- **Newsletter subscription / unsubscribe**
- **Sitemap** (`/sitemap.xml`)
- **Dark mode**

### Admin (`/admin`)

- Dashboard with platform stats
- Anime CRUD — with bulk delete and CSV import
- Episode CRUD per anime
- Genre and Tag management
- Studio management
- Quote management
- Review moderation
- Subscriber management and newsletter dispatch
- Contact messages
- Audit log with CSV/PDF export
- **Jikan import panel** — search, import by MAL ID, import top anime, import by season

---

## Installation

```bash
git clone https://github.com/blutbaden/anime-shelf.git
cd anime-shelf

composer install
cp .env.example .env
php artisan key:generate
```

Configure your `.env`:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=anime_shelf
DB_USERNAME=your_user
DB_PASSWORD=your_password

CACHE_STORE=database
```

Then run:

```bash
php artisan migrate
php artisan storage:link
npm install
npm run build
php artisan serve
```

Or use the all-in-one dev script (server + queue + logs + Vite hot reload):

```bash
composer dev
```

---

## Jikan Integration

[Jikan](https://jikan.moe) is an unofficial REST API for [MyAnimeList](https://myanimelist.net). Anime Shelf uses it to bulk-import anime data including titles, synopsis, cover images, genres, studios, ratings, air dates, trailers, and more.

### Configuration

Add to your `.env`:

```env
JIKAN_BASE_URL=https://api.jikan.moe/v4
```

And in `config/services.php`:

```php
'jikan' => [
    'base_url' => env('JIKAN_BASE_URL', 'https://api.jikan.moe/v4'),
],
```

### Admin Panel Import

Go to **Admin → Jikan Import** (`/admin/jikan`) to:

- **Search** anime by title and import individual entries
- **Import by MAL ID** — import a specific anime directly using its MyAnimeList ID
- **Import Top Anime** — fetch the most popular anime from MAL
- **Import by Season** — import all anime from a specific season and year

### Artisan Commands

**Import a season:**

```bash
# Current season (auto-detected)
php artisan jikan:import-season

# Specific season
php artisan jikan:import-season --year=2024 --season=fall

# Walk back N previous seasons
php artisan jikan:import-season --past=4
```

**Auto-detect seasons count** (uses Jikan relations API):

```bash
# Update all anime where seasons is not yet set
php artisan jikan:sync-seasons

# Force re-sync all imported anime
php artisan jikan:sync-seasons --force
```

### How Seasons Are Detected

Jikan's relations endpoint (`/anime/{id}/relations`) returns sequel entries. Each anime-type sequel represents an additional season. The formula used is:

```
seasons = 1 (the anime itself) + count of sequel anime entries
```

> **Note:** This works well for anime where each season is a separate MAL entry (e.g. Attack on Titan, Demon Slayer). For long-running single-entry series (e.g. Naruto Shippuden — 500 episodes as one MAL entry), the "seasons" as defined by streaming platforms like Netflix will not match MAL data. In those cases, set the value manually via the admin edit form.

### Rate Limiting

Jikan allows ~3 requests per second. The service automatically sleeps 350 ms between requests. Bulk imports may take several minutes depending on the number of anime being fetched.

---

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_URL` | Application URL — must match the server port | `http://localhost:8000` |
| `DB_CONNECTION` | Database driver | `pgsql` |
| `CACHE_STORE` | Cache driver (`database` recommended) | `database` |
| `JIKAN_BASE_URL` | Jikan API base URL | `https://api.jikan.moe/v4` |
| `GITHUB_CLIENT_ID` | GitHub OAuth app ID | — |
| `GITHUB_CLIENT_SECRET` | GitHub OAuth secret | — |
| `GOOGLE_CLIENT_ID` | Google OAuth client ID | — |
| `GOOGLE_CLIENT_SECRET` | Google OAuth secret | — |
| `MAIL_*` | Mail driver config for newsletters | — |

---

## License

MIT
