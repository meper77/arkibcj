# CLAUDE.md

Instructions for AI assistants and operators working in this repo.

## What this is

**Sistem Arkib UiTM** — a Laravel 12 / PHP 8.2 records-management app (reference
numbers, files, *pemisahan rekod*, *pelupusan rekod*) served at the intranet host
`e-arkibcj.uitm.edu.my` (Hestia panel on `10.0.26.208`, nginx + php-fpm, MySQL).
The app itself lives in `arkib-app/`; `res/` holds DOCX print templates.

> The app was **downgraded from Laravel 13 to Laravel 12** because the Hestia
> server maxes out at PHP 8.2 and Laravel 13 requires `php >= 8.3`. `composer.json`
> pins `laravel/framework: ^12.0`, `php: ^8.2`, `phpunit/phpunit: ^11.5`, and
> `config.platform.php: 8.2.0` so the lock always resolves to an 8.2-safe set.

## Deploy pipeline (CI/CD)

`git push origin main` → GitHub workflow on the **self-hosted runner**
`J1-OMEGA-30-arkib` (UiTM LAN) → build → `rclone sync` over SFTP to Hestia.

- Workflow: `.github/workflows/deploy-to-hestia.yml`
- A self-hosted runner is required: Hestia (`10.0.26.208`) and the subdomain are
  intranet-only, unreachable from GitHub cloud runners.
- Build runs on the runner: `composer install --no-dev` + `npm ci && npm run build`.
- Secrets live on the runner, not GitHub: the full `.env` is at
  `C:\deploy\arkib\.env` and is copied into `arkib-app/.env` at build time.
- rclone SFTP remote `hestia-arkib` → `arkibinfo@10.0.26.208`, key
  `~/.ssh/hestia_arkib_deploy_key` (host keys pinned in `~/.ssh/known_hosts`).

Manual deploy from the runner: `./deploy/deploy.ps1`
(`-PrivateOnly` stages the app without flipping `public_html`; `-SkipMigrate`
uploads without running migrations).

## Server layout (important — this is a Laravel app, not flat PHP)

nginx serves `public_html` as the document root and **ignores `.htaccess`**. The
app is deployed *outside* the web root and `public_html` holds only Laravel's
`public/`:

```
web/e-arkibcj.uitm.edu.my/
├── private/arkib/     <- Laravel app: app, vendor, config, database, routes,
│                         resources, storage, .env, artisan, composer.*
└── public_html/       <- Laravel public/: build/, images/, favicon, robots.txt
    ├── index.php      <- deploy/index.php (patched: boots ../private/arkib,
    │                     usePublicPath(__DIR__)); overwritten each deploy
    └── __deploy.php   <- deploy/__deploy.php (guarded migrate/optimize runner)
```

- `deploy/index.php` requires `../private/arkib/vendor/autoload.php` +
  `bootstrap/app.php`, then `usePublicPath(__DIR__)` so `public_path()` and the
  Vite manifest resolve to `public_html`.
- The domain's nginx template must front-controller to `index.php`
  (`try_files $uri $uri/ /index.php?$query_string;`), or Laravel routes 404.

## Database & migrations

Production is MySQL (localhost-only on the Hestia box — **not** reachable from the
runner). Migrations therefore run **server-side** via the guarded endpoint:

```
GET /__deploy.php?key=<DEPLOY_KEY>&action=migrate      # migrate --force
GET /__deploy.php?key=<DEPLOY_KEY>&action=optimize     # optimize:clear; config:cache; view:cache
```

`__deploy.php` uses the **console** kernel (no HTTP session, so it works before
the `sessions` table exists) and reads `DEPLOY_KEY` straight from `.env`
(constant-time compare; 404 on mismatch). `route:cache` is never run — `/` is a
closure route. `deploy.ps1` calls these automatically after uploading.

`SESSION`, `CACHE`, and `QUEUE` drivers are all `database`, so the site will not
serve a page until migrations have created their tables.

## Backups

Before the first v1→v2 replace, the live `public_html` was copied server-side to
`public_html_v1_bak` (sibling dir) and pulled locally. To roll back: restore
`public_html_v1_bak` over `public_html`.

## Build toolchain (on the runner `J1-OMEGA-30`)

- PHP 8.2: `C:\Users\User.J1-ALPHA-PENS\php82\php.exe` (php.ini enables sodium,
  pdo_mysql, mbstring, gd, intl, zip, xsl, curl, fileinfo, openssl, …).
- Composer: `…\PHP.PHP.8.3_…\composer.phar` (phar is version-independent).
- Node/npm on PATH; rclone on PATH.

## Gotchas

- Don't `composer update` casually — the lock is pinned to a Laravel 12 / PHP 8.2
  set (`config.platform.php: 8.2.0`); this server has no PHP 8.3+.
- `vendor/`, `arkib-app/public/build/`, and `.env` are gitignored; CI regenerates
  the first two and restores `.env` from the runner secret store.
- `rclone sync` mirrors deletions, but `storage/**` and `bootstrap/cache/**` are
  excluded from the app sync and seeded with `rclone copy`, so runtime data
  (sessions, logs, compiled views) is never clobbered.

## Operations (post-deploy)

- **Live:** http://e-arkibcj.uitm.edu.my — the domain serves HTTP (https redirects
  to http), so `APP_URL` and the deploy curls use `http://`.
- **Default superadmin:** `admin@uitm.edu.my` / `password` (seeded). **Change it
  immediately.**
- **`__deploy.php`** lives in `public_html` only during a deploy and is removed at
  the end (it can migrate/dump the DB). Actions: `migrate`, `optimize`,
  `optimize-clear`, `db-info`, `db-backup`, `migrate-fresh` (destructive — resets
  the DB).
- **v1→v2 cutover backups:** `public_html_v1_bak/` (server) holds the old site;
  the old MySQL data is dumped at
  `private/arkib/storage/app/backups/arkibinfo_db_*.sql` (server) and
  `C:\deploy\arkib\db-backups\` (runner). Restore with `mysql arkibinfo_db < dump.sql`.
- The app was developed on SQLite; the `pelupusan` FK migration was reordered to be
  MySQL-valid (SET NULL needs a nullable column first). Watch for similar
  SQLite-isms if adding migrations.
