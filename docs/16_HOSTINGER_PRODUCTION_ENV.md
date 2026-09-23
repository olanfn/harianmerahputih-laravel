# Production Environment Inventory

Create these values privately in hPanel. Never commit the production `.env`.

## Required

```text
APP_NAME=Harian Merah Putih
APP_ENV=production
APP_KEY=<generate/set privately during deployment>
APP_DEBUG=false
APP_URL=https://harianmerahputih.co.id
DB_CONNECTION=mysql
DB_HOST=<hPanel value>
DB_PORT=3306
DB_DATABASE=<hPanel value>
DB_USERNAME=<hPanel value>
DB_PASSWORD=<secret>
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
ADMIN_PATH=<private configured path>
```

## Mail variables

`MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`/`MAIL_SCHEME`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME` are required only when password reset email is enabled in production. Newsletter campaign sending is not implemented.

## Optional/not used

AWS/S3 variables, Redis variables, and Node/Vite runtime variables are not required by the production application. `APP_KEY` must be unique to production; it is not generated in this phase.

