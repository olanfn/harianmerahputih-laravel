# Scheduled Publishing

Scheduled articles reuse the existing `articles.status` and `articles.published_at` columns. No schema change is required. When editors choose `scheduled`, `published_at` is the intended WIB publication time.

The eligibility predicate is:

```text
status = scheduled
published_at IS NOT NULL
published_at <= now()
```

The command `php artisan articles:publish-scheduled` processes due articles in chunks of 100. Each row is changed with a conditional MySQL update (`status=scheduled`) so repeated or concurrent executions cannot publish the same row twice. The scheduler also uses `withoutOverlapping()` with the existing file cache.

On success, status becomes `published`; the existing `published_at` is retained as the scheduled/publication timestamp. An `article.auto_published` audit event stores only status metadata. Individual failures are reported and do not stop other rows.

Hostinger only needs the existing one-minute Cron:

```text
* * * * * cd /home/ACCOUNT/harianmerahputih && php artisan schedule:run >> /dev/null 2>&1
```

Replace `ACCOUNT` and the path with the actual hPanel path. Before the command runs, scheduled future articles remain excluded by the central `published()` scope; after publication they naturally appear in the article URL, sitemap, news sitemap, and RSS.

There is no separate cancel-schedule action. Editors can edit a scheduled article and change it back to draft/review according to their role. No migration or queue worker is required.

