# Arkib — UiTM Archive System

A records management web application for UiTM (Universiti Teknologi MARA) — tracks reference numbers, files, record separation (*pemisahan rekod*), and disposal (*pelupusan rekod*) workflows, with printable forms generated from official templates.

## Modules

| Page | Purpose |
| --- | --- |
| **No. Rujukan** | Register reference numbers (single or CSV batch); format `SIRI-UiTM(KOD. NOMBOR)` |
| **Fail** | Register files against a reference number, track volumes, box, and person in charge |
| **Pemisahan Rekod** | Group files by box; record separation date and purpose; print separation forms and box labels (Pentadbiran / Staf / Pelajar) |
| **Pelupusan** | Status workflow (Pending → Approve / Decline → Lupus); print disposal forms |
| **User Management** | Superadmin registration, PTRJ / PRJ position assignment, profile-driven form inheritance |

## Stack

**Backend**
- PHP 8.3 · Laravel 13
- Laravel Breeze (auth scaffold)
- Laravel Passport (OAuth2 / API tokens)
- Laravel MCP (AI tooling integration)
- PhpSpreadsheet · PhpWord (CSV import / DOCX generation)
- SQLite (dev) — driver-agnostic via Eloquent

**Frontend**
- Blade templates
- Tailwind CSS 3 + `@tailwindcss/forms`
- Alpine.js 3
- Vite 8 (`laravel-vite-plugin`)
- Axios

**Dev Tooling**
- Laravel Pail (log tailing) · Pint (formatter) · PHPUnit 12
- `composer dev` — runs `serve`, `queue:listen`, `pail`, and `vite` concurrently

## Visual System

- **Layout** — Blade component layouts (`resources/views/layouts/`) with a navigation shell and guest/auth variants
- **Tables first** — every module opens on a data table (`BIL. | NO. RUJUKAN | PERKARA | …`); row click opens edit, header buttons trigger Daftar / Batch / Select / Print
- **Forms** — `forms0()` / `forms1()` / `forms2()` per module, enforcing uppercase rules, numeric-only fields, and unique `(NO. RUJUKAN, JILID)` constraints
- **Print templates** — DOCX templates in `res/` are hydrated with profile + row data:
  - `borangPemisahanRekod.docx` — separation form
  - `borangPelupusanRekod.docx` — disposal form
  - `labelFailPentadbiranLatest.docx` / `labelFailStafLatest.docx` / `labelFailPelajarLatest.docx` — box labels
- **Profile inheritance** — `KAMPUS` / `CAWANGAN` / `FAKULTI/BAHAGIAN` flow from the user profile into forms and printouts; `PERSON IN CHARGE` auto-fills the current account

## Quick Start

```bash
cd arkib-app
composer setup      # install, .env, key:generate, migrate, npm build
composer dev        # serve + queue + logs + vite
```

Default superadmin: `admin@uitm.edu.my` / `password`

## Layout

```
arkib/
├── arkib-app/          Laravel application
│   ├── app/Models/     Fail, NoRujukan, Pemisahan, Pelupusan, User
│   ├── resources/views/  auth, no-rujukan, fail, pemisahan, pelupusan, users, profile
│   └── routes/         web.php, auth.php, ai.php
├── res/                DOCX print templates (originals + working copies)
└── system.md           Canonical spec — do not delete
```

See `system.md` for the full functional specification.
