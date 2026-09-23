# Hostinger Premium Pre-deployment Checklist

- [ ] PHP 8.3+ selected.
- [ ] Enable ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pdo, pdo_mysql, session, tokenizer, and xml.
- [ ] Dedicated MySQL/MariaDB database and user created.
- [ ] Production `.env` created outside public exposure; `APP_KEY` kept secret.
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, and production `APP_URL` configured.
- [ ] Intended `ADMIN_PATH` configured.
- [ ] Domain document root points only to Laravel `public/`.
- [ ] Install/upload `vendor/` with `composer install --no-dev --optimize-autoloader`.
- [ ] Upload locally compiled `public/build/`.
- [ ] Make `storage/` and `bootstrap/cache/` writable without 777.
- [ ] Run `php artisan storage:link`.
- [ ] Run migrations only after a verified database backup.
- [ ] Verify database sessions and session table.
- [ ] Set `SESSION_SECURE_COOKIE=true` after HTTPS is active.
- [ ] Configure Cron with `php artisan schedule:run` every minute.
- [ ] Verify `articles:publish-scheduled` publishes due articles and leaves future schedules private.
- [ ] Configure SMTP privately.
- [ ] Verify HTTPS and HTTP-to-HTTPS behavior.
- [ ] Back up MySQL and uploaded media separately.
- [ ] Smoke-test public pages, admin login, media, sitemap, RSS, robots, and old slug redirects.
- [ ] Do not run development seeders in production.

Before deployment retain the previous source archive, database backup, and media backup. For application failure restore the previous source/assets; for migration failure stop and restore the approved database backup rather than running destructive rollback commands blindly.

