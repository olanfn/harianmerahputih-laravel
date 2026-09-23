# Hostinger Premium Requirements

| Requirement | Application requirement | Local evidence | Hostinger status | Owner action |
|---|---|---|---|---|
| PHP | Laravel 12, Composer PHP `^8.2` | PHP 8.4.22 | OWNER_VERIFICATION_REQUIRED | Select PHP 8.3 or 8.4 |
| Required extensions | ctype, dom, fileinfo, filter, hash, mbstring, openssl, pdo, pdo_mysql, session, tokenizer, xml | Composer check passes locally; pdo_mysql is required by DB config | OWNER_VERIFICATION_REQUIRED | Enable/check in hPanel |
| MySQL/MariaDB | Laravel MySQL driver, utf8mb4 | MySQL connection and migrations pass | OWNER_VERIFICATION_REQUIRED | Obtain DB host/version |
| Sessions | Database session table | `SESSION_DRIVER=database`, migrations applied | SUPPORTED_BY_APPLICATION | Verify session table after migration |
| Cache | File cache | `CACHE_STORE=file` | SUPPORTED_BY_APPLICATION | Keep file cache |
| Queue | Sync queue | `QUEUE_CONNECTION=sync` | SUPPORTED_BY_APPLICATION | No worker needed |
| Node.js | Local build only | `public/build/manifest.json` generated | NOT_REQUIRED | Upload compiled build |
| Composer | Production vendor install | Composer lock available | OWNER_VERIFICATION_REQUIRED | Check SSH/Composer; otherwise upload compatible vendor |
| Storage | `storage/app/public` + `public/storage` link | Local link is active | OWNER_VERIFICATION_REQUIRED | Verify symlink support |
| Cron | `schedule:run` every minute | Scheduler lists publishing and cleanup | OWNER_VERIFICATION_REQUIRED | Verify interval and CLI PHP |
| SSL | HTTPS production | Cannot verify locally | OWNER_VERIFICATION_REQUIRED | Verify certificate in hPanel |
| Redis/Supervisor/PM2/Docker | None | No runtime usage | NOT_REQUIRED | Do not provision |

MySQL uses `utf8mb4` and `utf8mb4_unicode_ci`. Upload processing uses PHP image/file inspection; GD, Imagick, and Intervention Image are not required by current code. Recommended PHP settings for multi-upload are at least `upload_max_filesize=10M`, `post_max_size` above the total batch size, and adequate `memory_limit`, `max_execution_time`, and `max_file_uploads`; actual Hostinger limits require owner verification.

