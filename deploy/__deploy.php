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
    $kernel->bootstrap();

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
        case 'db-info':
            // read-only: list existing tables + row counts
            $conn = $app->make('db')->connection();
            $rows = $conn->select('SHOW TABLES');
            if (! $rows) { echo "(database is EMPTY - no tables)\n"; break; }
            $col = array_key_first((array) $rows[0]);
            echo count($rows) . " tables in " . $conn->getDatabaseName() . ":\n";
            foreach ($rows as $r) {
                $name = ((array) $r)[$col];
                try { $c = $conn->table($name)->count(); } catch (Throwable $e) { $c = '?'; }
                echo '  ' . str_pad($name, 45) . $c . " rows\n";
            }
            break;
        case 'db-backup':
            // Pure-PHP mysqldump-equivalent -> storage/app/backups/<db>_<ts>.sql
            $conn   = $app->make('db')->connection();
            $pdo    = $conn->getPdo();
            $dbName = $conn->getDatabaseName();
            $dir    = $appBase . '/storage/app/backups';
            if (! is_dir($dir)) { @mkdir($dir, 0775, true); }
            $file = $dir . '/' . $dbName . '_' . date('Ymd_His') . '.sql';
            $fh   = fopen($file, 'w');
            fwrite($fh, "-- backup of {$dbName} at " . date('c') . "\nSET FOREIGN_KEY_CHECKS=0;\n");
            $tblRows = $conn->select('SHOW TABLES');
            $col     = array_key_first((array) $tblRows[0]);
            $rowTotal = 0;
            foreach ($tblRows as $tr) {
                $t   = ((array) $tr)[$col];
                $ddl = (array) $conn->select("SHOW CREATE TABLE `{$t}`")[0];
                $sql = $ddl['Create Table'] ?? $ddl['Create View'] ?? '';
                fwrite($fh, "\n-- ---- {$t} ----\nDROP TABLE IF EXISTS `{$t}`;\n{$sql};\n");
                foreach ($conn->table($t)->get() as $row) {
                    $vals = array_map(function ($v) use ($pdo) {
                        if ($v === null)             { return 'NULL'; }
                        if (is_int($v) || is_float($v)) { return $v; }
                        return $pdo->quote((string) $v);
                    }, (array) $row);
                    fwrite($fh, "INSERT INTO `{$t}` VALUES (" . implode(',', $vals) . ");\n");
                    $rowTotal++;
                }
            }
            fwrite($fh, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            fclose($fh);
            echo "Backed up {$dbName}: " . count($tblRows) . " tables, {$rowTotal} rows\n";
            echo "File: storage/app/backups/" . basename($file) . " (" . filesize($file) . " bytes)\n";
            break;
        case 'migrate-fresh':
            // DESTRUCTIVE: drops every table, rebuilds Laravel schema, seeds superadmin.
            $run('migrate:fresh', ['--force' => true, '--seed' => true]);
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
