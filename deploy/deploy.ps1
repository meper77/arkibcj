#requires -Version 5
<#
Deploy arkib (Laravel) to Hestia over SFTP using the rclone remote 'hestia-arkib'.

Assumes the build is already done (arkib-app/vendor and arkib-app/public/build
exist) and arkib-app/.env has real MySQL creds. Called by the GitHub Actions
workflow, and usable standalone:

    .\deploy\deploy.ps1               # full deploy: app -> private/arkib,
                                      #   public -> public_html, then migrate+optimize
    .\deploy\deploy.ps1 -PrivateOnly  # only stage the app to private/arkib
                                      #   (no public_html flip, no migrate)
    .\deploy\deploy.ps1 -SkipMigrate  # deploy files but skip migrate/optimize

Server layout produced:
    web/e-arkibcj.uitm.edu.my/
      private/arkib/     <- Laravel app (app, vendor, config, .env, storage, ...)
      public_html/       <- Laravel public/ + patched index.php + __deploy.php
#>
param(
    [switch]$PrivateOnly,
    [switch]$SkipMigrate
)
$ErrorActionPreference = 'Stop'

$RepoRoot = Split-Path $PSScriptRoot -Parent
$App      = Join-Path $RepoRoot 'arkib-app'
$Remote   = 'hestia-arkib:web/e-arkibcj.uitm.edu.my'
$AppDest  = "$Remote/private/arkib"
$WebDest  = "$Remote/public_html"
$Domain   = 'e-arkibcj.uitm.edu.my'
$HostIp   = '10.0.26.208'

function Invoke-Rclone {
    param([Parameter(ValueFromRemainingArguments = $true)][string[]]$RArgs)
    Write-Host "rclone $($RArgs -join ' ')" -ForegroundColor Cyan
    & rclone @RArgs
    if ($LASTEXITCODE -ne 0) { throw "rclone failed ($LASTEXITCODE)" }
}

# --- Pre-flight ---------------------------------------------------------------
if (-not (Test-Path "$App\vendor\autoload.php"))        { throw "vendor/ missing - run composer install first" }
if (-not (Test-Path "$App\public\build\manifest.json")) { throw "public/build missing - run 'npm run build' first" }
if (-not (Test-Path "$App\.env"))                       { throw ".env missing in arkib-app/" }
if (Select-String -Path "$App\.env" -Pattern '__ARKIB_DB' -Quiet) {
    throw ".env still contains placeholder DB creds (__ARKIB_DB*). Fill real MySQL creds first."
}

$commonExcludes = @(
    '--exclude', '.git/**',
    '--exclude', '.github/**',
    '--exclude', 'node_modules/**',
    '--exclude', 'tests/**',
    '--exclude', 'storage/**',
    '--exclude', 'bootstrap/cache/**',
    '--exclude', '.env.example',
    '--exclude', 'README.md',
    '--exclude', 'phpunit.xml',
    '--exclude', '.editorconfig',
    '--exclude', '.gitattributes'
)

# --- 1. App code -> private/arkib (mirrors deletions; keeps storage & caches) --
Write-Host "== Sync app -> $AppDest ==" -ForegroundColor Green
Invoke-Rclone sync "$App" $AppDest --sftp-disable-hashcheck --transfers 8 --checkers 8 `
    --stats-one-line --exclude 'public/**' @commonExcludes

# --- 2. Seed persistent dirs (copy = never deletes runtime data) --------------
Write-Host "== Seed storage + bootstrap/cache (no delete) ==" -ForegroundColor Green
Invoke-Rclone copy "$App\storage"        "$AppDest/storage"        --sftp-disable-hashcheck --stats-one-line
Invoke-Rclone copy "$App\bootstrap\cache" "$AppDest/bootstrap/cache" --sftp-disable-hashcheck --stats-one-line

if ($PrivateOnly) {
    Write-Host "PrivateOnly: app staged to private/arkib. Skipped public_html flip + migrate." -ForegroundColor Yellow
    return
}

# --- 3. Flip public_html -> Laravel public (replaces v1; backup already taken) -
Write-Host "== Flip public_html -> Laravel public ==" -ForegroundColor Green
Invoke-Rclone sync "$App\public" $WebDest --sftp-disable-hashcheck --transfers 8 --checkers 8 `
    --stats-one-line --exclude 'index.php'
# Patched front controller + guarded deploy runner (overwrite each deploy).
Invoke-Rclone copyto "$PSScriptRoot\index.php"    "$WebDest/index.php"    --sftp-disable-hashcheck
Invoke-Rclone copyto "$PSScriptRoot\__deploy.php" "$WebDest/__deploy.php" --sftp-disable-hashcheck

if ($SkipMigrate) {
    Write-Host "SkipMigrate: files deployed, migrate/optimize skipped." -ForegroundColor Yellow
    return
}

# --- 4. Trigger migrate + optimize via the guarded endpoint -------------------
$m = Select-String -Path "$App\.env" -Pattern '^\s*DEPLOY_KEY\s*=\s*(.*)$'
$deployKey = if ($m) { $m.Matches.Groups[1].Value.Trim(" `t`"'") } else { '' }
if (-not $deployKey) { throw "DEPLOY_KEY not found in .env" }

function Invoke-DeployAction {
    param([string]$Action)
    $url = "https://$HostIp/__deploy.php?key=$deployKey&action=$Action"
    Write-Host "== trigger: $Action ==" -ForegroundColor Green
    $out = & curl.exe -sk --max-time 180 -H "Host: $Domain" "$url"
    Write-Host $out
    if ($out -notmatch 'DONE') { throw "$Action did not report DONE" }
}
Invoke-DeployAction 'migrate'
Invoke-DeployAction 'optimize'

Write-Host "Deployed OK -> https://$Domain/" -ForegroundColor Green
