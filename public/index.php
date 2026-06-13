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

// Lightweight idempotent migrations for existing installs (additive only).
try {
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `password_resets` (
        `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`      BIGINT UNSIGNED NOT NULL,
        `token_hash`   CHAR(64) NOT NULL,
        `expires_at`   DATETIME NOT NULL,
        `used_at`      DATETIME DEFAULT NULL,
        `requested_ip` VARCHAR(45) DEFAULT NULL,
        `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `password_resets_token_unique` (`token_hash`),
        KEY `password_resets_user_idx` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `payments` (
        `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`           BIGINT UNSIGNED NOT NULL,
        `package_id`        BIGINT UNSIGNED NOT NULL,
        `gateway`           VARCHAR(40) NOT NULL DEFAULT 'razorpay',
        `gateway_order_id`  VARCHAR(120) DEFAULT NULL,
        `gateway_payment_id`VARCHAR(120) DEFAULT NULL,
        `gateway_signature` VARCHAR(255) DEFAULT NULL,
        `amount`            DECIMAL(10,2) NOT NULL DEFAULT 0,
        `currency`          VARCHAR(8) NOT NULL DEFAULT 'INR',
        `status`            ENUM('created','paid','failed','refunded') NOT NULL DEFAULT 'created',
        `notes`             TEXT,
        `subscription_id`   BIGINT UNSIGNED DEFAULT NULL,
        `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `payments_user_idx` (`user_id`),
        KEY `payments_order_idx` (`gateway_order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    foreach ([
        'razorpay_enabled'        => '0',
        'razorpay_mode'           => 'test',
        'razorpay_key_id'         => '',
        'razorpay_key_secret'     => '',
        'razorpay_webhook_secret' => '',
    ] as $k => $v) {
        DB::q("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)", [$k, $v]);
    }
} catch (Throwable $e) { /* harmless on first install */ }

csrf_check();

$r = new Router();
require __DIR__ . '/../app/routes.php';
require __DIR__ . '/../app/admin_routes.php';

$r->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
