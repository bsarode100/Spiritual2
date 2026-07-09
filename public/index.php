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

    // ---- Additive columns for durable data capture (safe on both fresh + existing installs) ----
    // Each ALTER is wrapped so pre-existing columns don't throw.
    foreach ([
        // users: verification + activity signals
        "ALTER TABLE `users` ADD COLUMN `email_verified_at` DATETIME NULL AFTER `status`",
        "ALTER TABLE `users` ADD COLUMN `phone_verified_at` DATETIME NULL AFTER `email_verified_at`",
        "ALTER TABLE `users` ADD COLUMN `last_active_at`    DATETIME NULL AFTER `last_login_at`",

        // profiles: privacy + fields families ask for
        "ALTER TABLE `profiles` ADD COLUMN `visibility` ENUM('public','members','hidden') NOT NULL DEFAULT 'members' AFTER `partner_pref`",
        "ALTER TABLE `profiles` ADD COLUMN `show_phone` TINYINT(1) NOT NULL DEFAULT 0 AFTER `visibility`",
        "ALTER TABLE `profiles` ADD COLUMN `show_email` TINYINT(1) NOT NULL DEFAULT 0 AFTER `show_phone`",
        "ALTER TABLE `profiles` ADD COLUMN `willing_to_relocate` TINYINT(1) NOT NULL DEFAULT 0 AFTER `show_email`",
        "ALTER TABLE `profiles` ADD COLUMN `body_type` VARCHAR(40) NULL AFTER `height_cm`",
        "ALTER TABLE `profiles` ADD COLUMN `complexion` VARCHAR(40) NULL AFTER `body_type`",
        "ALTER TABLE `profiles` ADD COLUMN `blood_group` VARCHAR(10) NULL AFTER `complexion`",
        "ALTER TABLE `profiles` ADD COLUMN `no_of_siblings` VARCHAR(20) NULL AFTER `family_status`",
        "ALTER TABLE `profiles` ADD COLUMN `father_occupation` VARCHAR(150) NULL AFTER `no_of_siblings`",
        "ALTER TABLE `profiles` ADD COLUMN `mother_occupation` VARCHAR(150) NULL AFTER `father_occupation`",

        // photos: moderation + private-photo gating (default approved so nothing existing hides)
        "ALTER TABLE `photos` ADD COLUMN `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved' AFTER `is_primary`",
        "ALTER TABLE `photos` ADD COLUMN `is_private` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }

    // ---- New tables for features the UI will grow into ----
    // Block a member: they can't see us, we can't see them, no interests/messages either way.
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `blocked_users` (
        `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`        BIGINT UNSIGNED NOT NULL,
        `blocked_user_id` BIGINT UNSIGNED NOT NULL,
        `reason`         VARCHAR(255) DEFAULT NULL,
        `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `blocked_pair_unique` (`user_id`,`blocked_user_id`),
        KEY `blocked_target_idx` (`blocked_user_id`),
        CONSTRAINT `blocked_user_fk`   FOREIGN KEY (`user_id`)         REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `blocked_target_fk` FOREIGN KEY (`blocked_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Member-flagged profiles — admin queue for trust/safety.
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `profile_reports` (
        `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `reporter_user_id` BIGINT UNSIGNED NOT NULL,
        `reported_user_id` BIGINT UNSIGNED NOT NULL,
        `category`         VARCHAR(60) NOT NULL,
        `details`          TEXT,
        `status`           ENUM('open','reviewing','resolved','dismissed') NOT NULL DEFAULT 'open',
        `admin_notes`      TEXT,
        `resolved_at`      DATETIME DEFAULT NULL,
        `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `reports_reported_idx` (`reported_user_id`),
        KEY `reports_status_idx`   (`status`),
        CONSTRAINT `reports_reporter_fk` FOREIGN KEY (`reporter_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `reports_reported_fk` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Bell-icon notifications (new interest, accepted, new message, payment, admin note).
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `notifications` (
        `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`    BIGINT UNSIGNED NOT NULL,
        `type`       VARCHAR(40) NOT NULL,
        `title`      VARCHAR(200) NOT NULL,
        `body`       VARCHAR(500) DEFAULT NULL,
        `link`       VARCHAR(255) DEFAULT NULL,
        `read_at`    DATETIME DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `notif_user_read_idx` (`user_id`, `read_at`),
        CONSTRAINT `notif_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Who viewed whose profile (already have a counter on profiles.views — this is the log).
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `profile_views_log` (
        `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `viewer_user_id` BIGINT UNSIGNED NOT NULL,
        `viewed_user_id` BIGINT UNSIGNED NOT NULL,
        `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `views_viewed_time_idx` (`viewed_user_id`, `created_at`),
        KEY `views_viewer_time_idx` (`viewer_user_id`, `created_at`),
        CONSTRAINT `views_viewer_fk` FOREIGN KEY (`viewer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `views_viewed_fk` FOREIGN KEY (`viewed_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Lightweight audit trail — admin actions, blocks, logins from new IPs, payment status flips.
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `audit_log` (
        `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `actor_id`   BIGINT UNSIGNED DEFAULT NULL,
        `action`     VARCHAR(80) NOT NULL,
        `target_type` VARCHAR(40) DEFAULT NULL,
        `target_id`  BIGINT UNSIGNED DEFAULT NULL,
        `meta`       TEXT,
        `ip`         VARCHAR(45) DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `audit_actor_idx` (`actor_id`),
        KEY `audit_target_idx` (`target_type`, `target_id`),
        KEY `audit_created_idx` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (Throwable $e) { /* harmless on first install */ }

csrf_check();

$r = new Router();
require __DIR__ . '/../app/routes.php';
require __DIR__ . '/../app/admin_routes.php';

$r->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
