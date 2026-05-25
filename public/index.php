<?php
// Front controller for Spiritual Matrimony
declare(strict_types=1);

session_start();

require __DIR__ . '/../app/env.php';
load_env(__DIR__ . '/../.env');

$CFG = require __DIR__ . '/../app/config.php';
$GLOBALS['CFG'] = $CFG;

date_default_timezone_set($CFG['app']['timezone']);
if ($CFG['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

require __DIR__ . '/../app/DB.php';
require __DIR__ . '/../app/Auth.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/Router.php';

DB::init($CFG['db']);

// Auto-installer: if the users table is missing, run schema.sql once.
try {
    DB::val('SELECT 1 FROM users LIMIT 1');
} catch (PDOException $e) {
    $schema = file_get_contents(__DIR__ . '/../sql/schema.sql');
    // Split on ";\n" (statement boundary) — our schema has no embedded ";\n" inside strings.
    foreach (preg_split('/;\s*\n/', $schema) as $stmt) {
        $stmt = trim($stmt);
        if ($stmt !== '' && !str_starts_with($stmt, '--')) {
            DB::pdo()->exec($stmt);
        }
    }
    // hash seed passwords (schema ships with a placeholder hash)
    $admin = password_hash('admin@123', PASSWORD_BCRYPT);
    $mem   = password_hash('member@123', PASSWORD_BCRYPT);
    DB::q("UPDATE users SET password_hash = ? WHERE role = 'admin'", [$admin]);
    DB::q("UPDATE users SET password_hash = ? WHERE role = 'member'", [$mem]);
}

csrf_check();

$r = new Router();
require __DIR__ . '/../app/routes.php';
require __DIR__ . '/../app/admin_routes.php';

$r->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
