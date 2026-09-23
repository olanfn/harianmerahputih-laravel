# Hostinger Deployment Decision

| Area | Decision | Evidence/condition |
|---|---|---|
| PHP | OWNER_CHECK | Composer requires PHP `^8.2`; verify 8.3/8.4 in hPanel |
| Extensions | OWNER_CHECK | Local platform checks pass; hPanel availability unknown |
| MySQL | OWNER_CHECK | Local MySQL works; Hostinger version/host unknown |
| Composer | OWNER_CHECK | Prefer SSH Composer; otherwise compatible vendor archive |
| Document root | OWNER_CHECK | `public/` required; hPanel capability unknown |
| Storage | OWNER_CHECK | `storage:link` required; symlink capability unknown |
| Cron | OWNER_CHECK | Scheduler requires every-minute invocation; interval unknown |
| SSL | OWNER_CHECK | Required before secure cookies/HSTS |
| SMTP | OWNER_CHECK | Needed for production password reset mail only |
| Node.js | NOT_REQUIRED | Upload `public/build` |
| Redis | NOT_REQUIRED | File cache and sync queue |
| Permanent worker | NOT_REQUIRED | Queue is sync |
| SEO | PASS | Application SEO regression passed |
| Backup | OWNER_CHECK | Capture database, media, and release archive |

## Path A (preferred)

If hPanel supports a suitable document root and SSH/Composer: upload source, point domain to `public/`, run production Composer install, configure `.env`, migrate, link storage, cache config/routes/views, configure Cron, enable HTTPS, and smoke-test.

## Path B (fallback)

If `public_html` is forced, keep the Laravel core outside it and expose only public-facing files. Adjust `public_html/index.php` paths to the actual account layout. Do not implement paths until hPanel structure is known.

## Path C (last resort)

If server-side Composer is unavailable, upload a vendor directory built with a compatible PHP platform. This carries platform-extension risk and is less preferred than SSH Composer.

