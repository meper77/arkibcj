<?php

// Guarded deploy runner for arkib.
//
// Deployed to public_html/__deploy.php. Runs a FIXED, allow-listed set of
// artisan commands through the CONSOLE kernel, so it does NOT depend on the
// HTTP session (the sessions table may not exist yet on first migrate).
//
// Access (server-side, from the deploy runner):
//   /__deploy.php?key=<DEPLOY_KEY>&action=migrate|optimize|optimize-clear
//
// The key is compared, in constant time, against DEPLOY_KEY read directly from
// the app's .env (not via env(), which returns null once config is cached).
// Any mismatch returns 404 and reveals nothing.

$appBase = __DIR__ . '/../private/arkib';

// --- Guard: read DEPLOY_KEY straight from .env, compare before doing anything.
$expected = '';
$envFile  = $appBase . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (preg_match('/^\s*DEPLOY_KEY\s*=\s*(.*)$/', $line, $m)) {
            $expected = trim($m[1], " \t\"'");
        }
    }
}
$provided = (string) ($_GET['key'] ?? '');
if ($expected === '' || ! hash_equals($expected, $provided)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit("Not Found\n");
}

header('Content-Type: text/plain; charset=utf-8');

try {
    require $appBase . '/vendor/autoload.php';
    $app    = require_once $appBase . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $run = function (string $cmd, array $params = []) use ($kernel) {
        $status = $kernel->call($cmd, $params);
        echo "\n\$ php artisan {$cmd} " . json_encode($params) . "  => exit {$status}\n";
        echo $kernel->output();
        return $status;
    };

    // route:cache is intentionally omitted (closure routes are not cacheable).
    switch ((string) ($_GET['action'] ?? 'migrate')) {
        case 'migrate':
            $run('migrate', ['--force' => true]);
            break;
        case 'optimize':
            $run('optimize:clear');
            $run('config:cache');
            $run('view:cache');
            break;
        case 'optimize-clear':
            $run('optimize:clear');
            break;
        default:
            http_response_code(400);
            echo "Unknown action\n";
            exit;
    }

    echo "\nDONE\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
