# NOTES — Sistem Arkib UiTM

## Architectural Decisions

### Pelupusan — lupus_at approach
The `pelupusan` table uses a `lupus_at` timestamp column (nullable) to track when a record was lupused, rather than a separate `lupus_kotak` table. A row with `lupus_at IS NULL` is "pending"; a row with `lupus_at IS NOT NULL` is "after-lupus." This keeps the data model simple and avoids a join for the after-lupus display.

### Auto-creation of pemisahan rows
When a Fail is created (single or CSV import), a corresponding `pemisahan` row is automatically created with `tarikh_pemisahan = NULL` and `tujuan_pemisahan = NULL`. This ensures all fail records appear in the Pemisahan Rekod page and can be kemaskini'd. Fail records without a `kotak` value appear under "TIADA NO. KOTAK" section.

### Auto-creation of pelupusan rows
When the Pelupusan index page loads, it checks for any `pemisahan` records (with a `kotak` assigned) that do not yet have a `pelupusan` row, and creates them with status=PENDING. This is done on page load rather than via a trigger/observer for simplicity.

### Lupus action restriction
The Lupus action is only available when a record's status is APPROVE. If status is PENDING or DECLINE, the Lupus button is not shown.

### Password length: exactly 8 characters
The spec says "strict to 8 characters." This is enforced as `strlen($value) === 8` using a custom closure rule for both registration and password change. Laravel's built-in `Password::defaults()` was replaced with this custom rule.

### PTRJ/PRJ uniqueness
At most one user can hold PTRJ and at most one can hold PRJ at any time. Enforced in RegisteredUserController, UserController (store and updatePosition), and Registration. The superadmin has `position = NULL` and is excluded.

### Position transfer
The profile page allows a user to transfer their current position to another user. The operation sets the current user's position to NULL and the target user's position to the transferred position. The target user must not hold the same position already (unless they hold the position being transferred).

### Kampus inheritance in No. Rujukan create
The KAMPUS field in the Daftar No. Rujukan form pre-fills with the current user's kampus value from their profile.

### No. Rujukan full format
Format: `{siri}-{kampus}{space}({kod_bahagian}. {nombor_fail})`
When `additional_space = true`, one space is inserted between kampus and the opening parenthesis.
Example without space: `100-UiTM(INFO. 1/1)`
Example with space: `100-UiTM (INFO. 1/1)`

### CSV template for fail batch import
The CSV uses `no_rujukan_id` (the database PK integer) rather than the full no. rujukan string, for simplicity and to avoid ambiguity. Users should download the no. rujukan list first to find IDs.

### Superadmin cannot register via /register
The Register page does not set `is_superadmin`. The only superadmin is seeded via `DatabaseSeeder`. Superadmin password reset is not available via the users management page (guarded).

### Passport / HasApiTokens removed from User model
The original User model used `HasApiTokens` from `laravel/passport`, which requires `App\Models\User` to `use HasApiTokens`. This was removed since the web app uses session auth, not API tokens. The `laravel/passport` package remains in composer.json but the trait was removed to avoid token table conflicts. If API auth is needed later, re-add the trait.

## Missing Print Templates
The following template files referenced in system.md do not exist in `/home/meper/arkib/res/`:
- `res/borangPemisahanRekod.doc` — replaced with clean HTML print view (`print-pemisahan.blade.php`)
- `res/labelFailPentadbiran.png` — replaced with clean HTML label card (`print-pentadbiran.blade.php`)
- `res/labelFailStaf.png` — replaced with clean HTML label card (`print-staf.blade.php`)
- `res/labelFailPelajar.png` — replaced with clean HTML label card (`print-pelajar.blade.php`)
- `res/borangPelupusanRekod.jpg` — replaced with clean HTML print view (`print.blade.php` in pelupusan)

All print views use `@media print` CSS and open in a new browser tab with a Print button.

## Profile: delete-user-form removed
The original Breeze profile included a "delete account" partial. This was removed from the new profile/edit.blade.php as the spec does not include self-deletion. The destroy route still exists for backward compatibility.

## Database
- Connection: SQLite (`database/database.sqlite` — pre-existing)
- No additional migration needed for users table (kampus, cawangan, fakulti_bahagian, position, is_superadmin were already present in the original migration)
