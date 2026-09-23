# hPanel Owner Checklist

Please verify these values in hPanel without sharing passwords, APP_KEY, or the private admin path:

- [ ] Plan is Hostinger Premium Shared Hosting.
- [ ] PHP 8.3/8.4 availability and selected version.
- [ ] Required extensions available, especially `pdo_mysql`, `fileinfo`, `mbstring`, `dom`, and `xml`.
- [ ] SSH available.
- [ ] Composer available over SSH.
- [ ] Domain document root can point to Laravel `public/`.
- [ ] Cron Jobs available and minimum interval.
- [ ] CLI PHP path/version.
- [ ] Symlink/storage link behavior.
- [ ] MySQL/MariaDB version.
- [ ] MySQL hostname format.
- [ ] SSL certificate active.
- [ ] `upload_max_filesize`.
- [ ] `post_max_size`.
- [ ] `memory_limit`.
- [ ] `max_execution_time`.
- [ ] `max_file_uploads`.
- [ ] SMTP availability.

Do not paste database password, SMTP password, APP_KEY, or ADMIN_PATH into reports.

