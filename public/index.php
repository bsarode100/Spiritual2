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

    // Load the long-form policy content (privacy, terms, refund, cookies).
    // Kept in a separate file so the admin can re-seed defaults from /admin/pages
    // by running this one file again. Uses ON DUPLICATE KEY UPDATE — idempotent.
    $seedFile = __DIR__ . '/../sql/pages_seed.sql';
    if (is_file($seedFile)) {
        foreach (preg_split('/;\s*\n/', file_get_contents($seedFile)) as $stmt) {
            $stmt = trim($stmt);
            if ($stmt !== '' && !str_starts_with($stmt, '--')) {
                try { DB::pdo()->exec($stmt); } catch (PDOException $e) { /* skip */ }
            }
        }
    }
}

// Lightweight idempotent migrations for existing installs (additive only).
try {
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `password_resets` (
        `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`      BIGINT UNSIGNED NOT NULL,
        `otp_hash`     CHAR(64) DEFAULT NULL,
        `token_hash`   CHAR(64) DEFAULT NULL,
        `attempts`     TINYINT UNSIGNED NOT NULL DEFAULT 0,
        `expires_at`   DATETIME NOT NULL,
        `used_at`      DATETIME DEFAULT NULL,
        `requested_ip` VARCHAR(45) DEFAULT NULL,
        `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `password_resets_user_idx` (`user_id`),
        UNIQUE KEY `password_resets_token_unique` (`token_hash`),
        KEY `password_resets_active_idx` (`user_id`, `used_at`, `expires_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    foreach ([
        "ALTER TABLE `password_resets` ADD COLUMN `otp_hash` CHAR(64) NULL AFTER `user_id`",
        "ALTER TABLE `password_resets` MODIFY `otp_hash` CHAR(64) NULL",
        "ALTER TABLE `password_resets` ADD COLUMN `token_hash` CHAR(64) NULL AFTER `otp_hash`",
        "ALTER TABLE `password_resets` MODIFY `token_hash` CHAR(64) NULL",
        "ALTER TABLE `password_resets` ADD COLUMN `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `token_hash`",
        "ALTER TABLE `password_resets` ADD KEY `password_resets_active_idx` (`user_id`, `used_at`, `expires_at`)",
        "ALTER TABLE `password_resets` ADD UNIQUE KEY `password_resets_token_unique` (`token_hash`)",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }

    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `signup_otps` (
        `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `email`        VARCHAR(190) NOT NULL,
        `otp_hash`     CHAR(64) NOT NULL,
        `attempts`     TINYINT UNSIGNED NOT NULL DEFAULT 0,
        `expires_at`   DATETIME NOT NULL,
        `used_at`      DATETIME DEFAULT NULL,
        `requested_ip` VARCHAR(45) DEFAULT NULL,
        `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `signup_otps_email_idx` (`email`),
        KEY `signup_otps_active_idx` (`email`, `used_at`, `expires_at`)
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
