# PHASE 10 — Security Hardening

Status: **in progress / not production-ready yet**.

## Implemented

- Role middleware protects the main Admin, Guru, Siswa and Kepala Sekolah route groups.
- Object-level authorization is enforced for `KelasMapel` and `WaliKelas` through policies and route `can:` middleware.
- Student task access performs ownership/class checks before viewing, submitting or downloading files.
- Teacher task/material routes verify the teacher owns the `KelasMapel` before operating on nested records.
- Uploads are stored on the private `local` disk instead of the public disk for task submissions/materials.
- Student task uploads now require both a safe extension and server-detected MIME type (`image/jpeg` or `application/pdf`) with a 5 MB per-file limit and a 5-file limit.
- Download responses sanitize the supplied download filename with `basename()`.
- Laravel's `web` middleware remains enabled for the application routes, preserving session, cookie and CSRF protection.
- Login attempts are rate limited to 5 attempts per minute per username/IP combination.
- A general per-route request limiter is now applied to web traffic.
- Security headers include CSP, clickjacking protection, MIME sniffing protection, Referrer-Policy, Permissions-Policy and COOP; HSTS is enabled in production over HTTPS.
- Authenticated responses are marked `no-store` to reduce sensitive browser/proxy caching.
- Production can force users with default credentials to change their password before accessing the application.
- Account password changes require current-password confirmation and a stronger password policy.
- Production environment controls are documented in `.env.example`.

## Audit findings still requiring closure

1. `UserController::exportExcel()` currently contains the legacy default password in the generated spreadsheet. This must be removed before production.
2. Legacy admin/siswa password-reset flows still depend on the application's default-password convention and should be changed to generated temporary credentials or a secure reset workflow.
3. A complete controller-by-controller authorization matrix should be executed against every parameterized route, not only the high-risk academic routes already covered by policies/ownership checks.
4. Full automated test execution must be performed on the deployment environment; this sandbox cannot execute the project's PHP dependencies.

## Production deployment requirements

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Generate a unique `APP_KEY`; never commit `.env`.
- Use HTTPS and set `SESSION_SECURE_COOKIE=true`.
- Keep `SESSION_HTTP_ONLY=true` and `SESSION_SAME_SITE=lax` unless the deployment architecture requires another value.
- Set `FORCE_PASSWORD_CHANGE=true` until all seeded/default accounts have changed credentials.
- Use a non-default database account with only the privileges required by the application.
- Ensure `storage/` and `bootstrap/cache/` are writable by the application process, while `.env` and source files are not web-writable.
- Keep private user/task files outside the public web root and serve them through authorized download controllers.
- Run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache` during deployment after environment configuration is finalized.
- Run the complete test suite and manually verify 401/403/404 behavior for each role before opening the system to school users.
