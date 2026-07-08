<?php

// Front controller for e-arkibcj.uitm.edu.my (Hestia + nginx).
//
// The Laravel application lives OUTSIDE the web root, at
//   web/e-arkibcj.uitm.edu.my/private/arkib
// while this file is the document root (web/e-arkibcj.uitm.edu.my/public_html).
// This keeps source, .env, vendor and storage out of the served directory.
//
// Deployed by deploy/deploy.ps1 as public_html/index.php (do not edit on the
// server; it is overwritten on every deploy).

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$appBase = __DIR__ . '/../private/arkib';

// Maintenance mode...
if (file_exists($maintenance = $appBase . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader (from the app dir outside the web root)...
require $appBase . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $appBase . '/bootstrap/app.php';

// This directory (public_html) is the real document root, not <app>/public,
// so point Laravel's public path here (asset(), Vite manifest lookup, etc.).
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
