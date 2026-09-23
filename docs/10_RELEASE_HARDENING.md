# Release Hardening Report

## Baseline

- Laravel 12.69.2, PHP 8.4.22 locally, MySQL, file cache, database sessions, and synchronous queue.
- All 18 migrations are applied. Owner changes in the working tree were preserved; no destructive command was run.
- Current automated suite: 38 tests and 235 assertions passing.

## Production configuration

Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL=https://harianmerahputih.co.id` in production. Keep `APP_KEY`, database, and mail credentials secret. The runtime does not require Redis, Supervisor, PM2, Docker, Node.js, or S3; upload the locally built `public/build`.

Sessions use the database driver with a 120-minute lifetime, HttpOnly cookies, `SameSite=Lax`, root path, and an environment-controlled secure flag. Set `SESSION_SECURE_COOKIE=true` only after HTTPS is active. `ADMIN_PATH` is configurable and is not an authorization boundary.

## Security review

- Login is server-rate-limited to five attempts per minute with generic invalid-credential responses.
- Passwords use Laravel hashing. Uploads validate image content, JPG/JPEG/PNG/WebP MIME types, 10 MB maximum, and 100–8000 pixel dimensions; filenames are generated server-side and SVG is rejected.
- Article, media, taxonomy, user, preview, and revision access use server-side authorization.
- Rich text is sanitized before trusted HTML rendering; normal Blade output remains escaped.
- Public queries exclude draft, review, future scheduled, archived, and demo content.
- Legacy article redirects are internal 301 redirects to published targets.

Verify response security headers at the application/web-server boundary before production. Enable HSTS only after HTTPS is confirmed. Introduce CSP separately after testing Vite, Alpine, branding, and media sources.

## Scheduler and release blocker

`schedule:run` runs temporary-media cleanup and `articles:publish-scheduled` every minute. The scheduled publisher uses the existing `status` and `published_at` fields, conditional updates, chunking, and scheduler overlap protection.

Recommended Hostinger Cron (replace the path):

```text
* * * * * cd /home/ACCOUNT/harianmerahputih && php artisan schedule:run >> /dev/null 2>&1
```

## Storage and QA

Run `php artisan storage:link`. Keep `storage/` and `bootstrap/cache/` writable by PHP with least privilege; never use 777. Only `public/` should be the document root. `about`, migration status, PHPUnit, build, and view cache pass locally. Pint fails on pre-existing formatting across many files; it was not mass-applied to avoid touching owner changes.

