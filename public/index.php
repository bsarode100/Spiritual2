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
        `package_id`        BIGINT UNSIGNED DEFAULT NULL,
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

    // ============================================================
    // Premium Membership System — schema migrations (idempotent)
    // ============================================================

    // Extend packages: plan key, ribbons, badges, monthly display, priority rank,
    // and the feature-flag matrix that drives feature gating throughout the app.
    foreach ([
        "ALTER TABLE `packages` ADD COLUMN `slug` VARCHAR(40) DEFAULT NULL AFTER `id`",
        "ALTER TABLE `packages` ADD UNIQUE KEY `packages_slug_unique` (`slug`)",
        "ALTER TABLE `packages` ADD COLUMN `duration_months` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `duration_days`",
        "ALTER TABLE `packages` ADD COLUMN `monthly_display` DECIMAL(10,2) DEFAULT NULL AFTER `price`",
        "ALTER TABLE `packages` ADD COLUMN `savings_badge` VARCHAR(40) DEFAULT NULL AFTER `monthly_display`",
        "ALTER TABLE `packages` ADD COLUMN `ribbon` VARCHAR(40) DEFAULT NULL AFTER `savings_badge`",
        "ALTER TABLE `packages` ADD COLUMN `priority_rank` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `ribbon`",
        "ALTER TABLE `packages` ADD COLUMN `interests_per_month` INT NOT NULL DEFAULT 10 AFTER `contacts_limit`",
        "ALTER TABLE `packages` ADD COLUMN `shortlist_limit` INT NOT NULL DEFAULT 20 AFTER `interests_per_month`",
        "ALTER TABLE `packages` ADD COLUMN `boosts_per_month` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `shortlist_limit`",
        "ALTER TABLE `packages` ADD COLUMN `featured_days` SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER `boosts_per_month`",
        "ALTER TABLE `packages` ADD COLUMN `always_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `featured_days`",
        "ALTER TABLE `packages` ADD COLUMN `advanced_search` TINYINT(1) NOT NULL DEFAULT 0 AFTER `always_featured`",
        "ALTER TABLE `packages` ADD COLUMN `see_who_viewed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `advanced_search`",
        "ALTER TABLE `packages` ADD COLUMN `see_who_shortlisted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `see_who_viewed`",
        "ALTER TABLE `packages` ADD COLUMN `unlimited_photos` TINYINT(1) NOT NULL DEFAULT 0 AFTER `see_who_shortlisted`",
        "ALTER TABLE `packages` ADD COLUMN `unlimited_search` TINYINT(1) NOT NULL DEFAULT 0 AFTER `unlimited_photos`",
        "ALTER TABLE `packages` ADD COLUMN `premium_badge` TINYINT(1) NOT NULL DEFAULT 0 AFTER `unlimited_search`",
        "ALTER TABLE `packages` ADD COLUMN `match_suggestions` VARCHAR(40) NOT NULL DEFAULT 'Basic' AFTER `premium_badge`",
        "ALTER TABLE `packages` ADD COLUMN `support_tier` VARCHAR(40) NOT NULL DEFAULT 'Email' AFTER `match_suggestions`",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }

    // Extend subscriptions with purchase/payment context and cancellation trail.
    foreach ([
        "ALTER TABLE `subscriptions` ADD COLUMN `payment_id` VARCHAR(120) DEFAULT NULL AFTER `payment_ref`",
        "ALTER TABLE `subscriptions` ADD COLUMN `purchased_at` DATETIME NULL AFTER `starts_at`",
        "ALTER TABLE `subscriptions` ADD COLUMN `cancelled_at` DATETIME NULL AFTER `status`",
        "ALTER TABLE `subscriptions` ADD COLUMN `granted_by_admin_id` BIGINT UNSIGNED NULL AFTER `cancelled_at`",
        "ALTER TABLE `subscriptions` ADD KEY `subs_status_end_idx` (`status`,`ends_at`)",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }

    // Extend payments to also cover addons + verification (packages already covered).
    foreach ([
        "ALTER TABLE `payments` DROP FOREIGN KEY `payments_pkg_fk`",
        "ALTER TABLE `payments` MODIFY `package_id` BIGINT UNSIGNED NULL",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }
    foreach ([
        "ALTER TABLE `payments` ADD COLUMN `purchase_type` ENUM('package','addon','verification') NOT NULL DEFAULT 'package' AFTER `package_id`",
        "ALTER TABLE `payments` ADD COLUMN `item_id` BIGINT UNSIGNED NULL AFTER `purchase_type`",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }

    // Per-user counters + badges the plan enforcement code reads on every action.
    foreach ([
        "ALTER TABLE `profiles` ADD COLUMN `featured_until` DATETIME NULL AFTER `visibility`",
        "ALTER TABLE `profiles` ADD COLUMN `boost_until` DATETIME NULL AFTER `featured_until`",
        "ALTER TABLE `profiles` ADD COLUMN `spotlight_until` DATETIME NULL AFTER `boost_until`",
        "ALTER TABLE `profiles` ADD COLUMN `verified_tier` ENUM('none','identity','selfie') NOT NULL DEFAULT 'none' AFTER `spotlight_until`",
        "ALTER TABLE `profiles` ADD COLUMN `verified_at` DATETIME NULL AFTER `verified_tier`",
        "ALTER TABLE `profiles` ADD COLUMN `extra_contact_credits` INT NOT NULL DEFAULT 0 AFTER `verified_at`",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }

    // Spiritual filters (Advanced Search) — kept on spiritual_details so the browse
    // JOIN already in place lights them up for free.
    foreach ([
        "ALTER TABLE `spiritual_details` ADD COLUMN `spiritual_organization` VARCHAR(120) DEFAULT NULL AFTER `mantra`",
        "ALTER TABLE `spiritual_details` ADD COLUMN `temple_visit_frequency` VARCHAR(60) DEFAULT NULL AFTER `spiritual_organization`",
        "ALTER TABLE `spiritual_details` ADD COLUMN `vegetarian` TINYINT(1) NOT NULL DEFAULT 0 AFTER `temple_visit_frequency`",
        "ALTER TABLE `spiritual_details` ADD COLUMN `vegan` TINYINT(1) NOT NULL DEFAULT 0 AFTER `vegetarian`",
        "ALTER TABLE `spiritual_details` ADD COLUMN `no_smoking` TINYINT(1) NOT NULL DEFAULT 0 AFTER `vegan`",
        "ALTER TABLE `spiritual_details` ADD COLUMN `no_alcohol` TINYINT(1) NOT NULL DEFAULT 0 AFTER `no_smoking`",
        "ALTER TABLE `spiritual_details` ADD COLUMN `scripture_preference` VARCHAR(120) DEFAULT NULL AFTER `no_alcohol`",
        "ALTER TABLE `spiritual_details` ADD COLUMN `festival_participation` VARCHAR(60) DEFAULT NULL AFTER `scripture_preference`",
        "ALTER TABLE `spiritual_details` ADD COLUMN `spiritual_lifestyle` VARCHAR(120) DEFAULT NULL AFTER `festival_participation`",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }

    // Contact views — needed to enforce contact_limit / month-window pack top-ups.
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `contact_views` (
        `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `viewer_user_id` BIGINT UNSIGNED NOT NULL,
        `viewed_user_id` BIGINT UNSIGNED NOT NULL,
        `subscription_id` BIGINT UNSIGNED NULL,
        `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `contact_views_pair_unique` (`viewer_user_id`,`viewed_user_id`,`subscription_id`),
        KEY `contact_views_viewer_idx` (`viewer_user_id`),
        CONSTRAINT `contact_views_viewer_fk` FOREIGN KEY (`viewer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `contact_views_viewed_fk` FOREIGN KEY (`viewed_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Add-on catalogue (admin editable).
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `addons` (
        `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `slug`         VARCHAR(60) NOT NULL,
        `name`         VARCHAR(120) NOT NULL,
        `description`  VARCHAR(255) DEFAULT NULL,
        `price`        DECIMAL(10,2) NOT NULL DEFAULT 0,
        `currency`     VARCHAR(8) NOT NULL DEFAULT 'INR',
        `kind`         ENUM('boost','spotlight','featured','contact_pack','review') NOT NULL,
        `duration_days` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        `quantity`     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
        `display_order` SMALLINT NOT NULL DEFAULT 0,
        `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `addons_slug_unique` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Ledger of purchased add-ons — even after they expire we can show history.
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `addon_purchases` (
        `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`    BIGINT UNSIGNED NOT NULL,
        `addon_id`   BIGINT UNSIGNED NOT NULL,
        `payment_id` BIGINT UNSIGNED NULL,
        `starts_at`  DATETIME NULL,
        `ends_at`    DATETIME NULL,
        `status`     ENUM('pending','active','expired','cancelled') NOT NULL DEFAULT 'pending',
        `amount`     DECIMAL(10,2) NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `addon_purchases_user_idx` (`user_id`),
        CONSTRAINT `addon_purchases_user_fk`  FOREIGN KEY (`user_id`)  REFERENCES `users`  (`id`) ON DELETE CASCADE,
        CONSTRAINT `addon_purchases_addon_fk` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Verification requests — completely separate from membership, admin approves manually.
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `verification_requests` (
        `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`     BIGINT UNSIGNED NOT NULL,
        `tier`        ENUM('identity','selfie') NOT NULL,
        `amount`      DECIMAL(10,2) NOT NULL DEFAULT 0,
        `payment_id`  BIGINT UNSIGNED NULL,
        `id_doc_path` VARCHAR(255) DEFAULT NULL,
        `selfie_path` VARCHAR(255) DEFAULT NULL,
        `status`      ENUM('pending_payment','pending_review','approved','rejected') NOT NULL DEFAULT 'pending_payment',
        `admin_notes` TEXT,
        `reviewed_at` DATETIME NULL,
        `reviewed_by` BIGINT UNSIGNED NULL,
        `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `verify_user_idx` (`user_id`),
        KEY `verify_status_idx` (`status`),
        CONSTRAINT `verify_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Verification document capture — govt ID + live selfie (photo or short video),
    // reviewed by an admin before the badge is granted. reject_reason is member-visible
    // (admin_notes stays internal). 'pending_upload' sits between payment and review;
    // a rejected request can be resubmitted, which moves it back to pending_review.
    foreach ([
        "ALTER TABLE `verification_requests` MODIFY `status`
            ENUM('pending_payment','pending_upload','pending_review','approved','rejected')
            NOT NULL DEFAULT 'pending_payment'",
        "ALTER TABLE `verification_requests` ADD COLUMN `id_doc_type` VARCHAR(40) NULL AFTER `payment_id`",
        "ALTER TABLE `verification_requests` ADD COLUMN `selfie_is_video` TINYINT(1) NOT NULL DEFAULT 0 AFTER `selfie_path`",
        "ALTER TABLE `verification_requests` ADD COLUMN `reject_reason` VARCHAR(255) NULL AFTER `admin_notes`",
        "ALTER TABLE `verification_requests` ADD COLUMN `submitted_at` DATETIME NULL AFTER `reject_reason`",
    ] as $stmt) {
        try { DB::pdo()->exec($stmt); } catch (Throwable $e) { /* already migrated */ }
    }
    // Legacy rows from before document capture existed: paid requests that reached
    // pending_review with nothing to review should ask the member for documents.
    try {
        DB::pdo()->exec("UPDATE verification_requests SET status = 'pending_upload'
                          WHERE status = 'pending_review' AND id_doc_path IS NULL");
    } catch (Throwable $e) { /* table exists but columns mid-migration — next boot fixes it */ }

    // Monthly interest counter — cheaper than COUNT(*) over the interests table on every send.
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `interest_counters` (
        `user_id`    BIGINT UNSIGNED NOT NULL,
        `year_month` CHAR(7) NOT NULL,
        `count`      INT NOT NULL DEFAULT 0,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`user_id`,`year_month`),
        CONSTRAINT `interest_counters_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Boost consumption ledger — one row per boost the user activates so we can
    // count "boosts used this month" per subscription.
    DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `profile_boosts` (
        `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`    BIGINT UNSIGNED NOT NULL,
        `source`     ENUM('plan','addon') NOT NULL DEFAULT 'plan',
        `starts_at`  DATETIME NOT NULL,
        `ends_at`    DATETIME NOT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `boosts_user_time_idx` (`user_id`,`created_at`),
        CONSTRAINT `boosts_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // ============================================================
    // Seed / upsert the 5 canonical membership plans + the Free plan.
    // Uses slug as the natural key so re-runs don't multiply rows.
    // ============================================================
    $planSeed = [
        [
            'slug' => 'free', 'name' => 'Free', 'tagline' => 'Get started, no payment',
            'price' => 0, 'monthly_display' => null, 'duration_days' => 36500, 'duration_months' => 0,
            'savings_badge' => null, 'ribbon' => null, 'highlighted' => 0, 'display_order' => 1,
            'priority_rank' => 1,
            'contacts_limit' => 0, 'interests_per_month' => 10, 'shortlist_limit' => 20,
            'boosts_per_month' => 0, 'featured_days' => 0, 'always_featured' => 0,
            'advanced_search' => 0, 'see_who_viewed' => 0, 'see_who_shortlisted' => 0,
            'unlimited_photos' => 0, 'unlimited_search' => 0, 'premium_badge' => 0,
            'match_suggestions' => 'Basic', 'support_tier' => 'Email',
            'features' => "Send 10 Interests per month\nLimited Profile Photos\nCannot View Contact Details\nCan Chat only after Interest Accepted\nBasic Search\nCan Shortlist up to 20 Profiles\nEmail Support\nBasic Match Suggestions",
        ],
        [
            'slug' => 'starter', 'name' => 'Starter Premium', 'tagline' => 'For the sincere seeker',
            'price' => 349, 'monthly_display' => 349, 'duration_days' => 30, 'duration_months' => 1,
            'savings_badge' => null, 'ribbon' => null, 'highlighted' => 0, 'display_order' => 2,
            'priority_rank' => 2,
            'contacts_limit' => 20, 'interests_per_month' => 0, 'shortlist_limit' => 0,
            'boosts_per_month' => 0, 'featured_days' => 0, 'always_featured' => 0,
            'advanced_search' => 0, 'see_who_viewed' => 1, 'see_who_shortlisted' => 0,
            'unlimited_photos' => 1, 'unlimited_search' => 1, 'premium_badge' => 0,
            'match_suggestions' => 'Basic', 'support_tier' => 'Email',
            'features' => "Unlimited Interests\nUnlimited Profile Photos\nView 20 Contact Details\nChat with Accepted Matches\nUnlimited Search\nBasic Search Filters\nSee Who Viewed Profile\nUnlimited Shortlists\nStandard Search Priority\nEmail Support\nBasic Match Suggestions",
        ],
        [
            'slug' => 'divine', 'name' => 'Divine Plus', 'tagline' => 'Everything in Starter plus advanced tools',
            'price' => 999, 'monthly_display' => 333, 'duration_days' => 90, 'duration_months' => 3,
            'savings_badge' => null, 'ribbon' => null, 'highlighted' => 0, 'display_order' => 3,
            'priority_rank' => 3,
            'contacts_limit' => 75, 'interests_per_month' => 0, 'shortlist_limit' => 0,
            'boosts_per_month' => 1, 'featured_days' => 7, 'always_featured' => 0,
            'advanced_search' => 1, 'see_who_viewed' => 1, 'see_who_shortlisted' => 1,
            'unlimited_photos' => 1, 'unlimited_search' => 1, 'premium_badge' => 0,
            'match_suggestions' => 'Weekly', 'support_tier' => 'Priority',
            'features' => "Everything in Starter Premium PLUS\nView 75 Contact Details\nAdvanced Search Filters\nSee Who Shortlisted You\nHigher Search Ranking\nFeatured Profile for 7 Days\nOne Profile Boost every Month\nWeekly Curated Matches\nPriority Support",
        ],
        [
            'slug' => 'soul_elite', 'name' => 'Soul Elite', 'tagline' => 'Most popular — for serious seekers',
            'price' => 1799, 'monthly_display' => 300, 'duration_days' => 180, 'duration_months' => 6,
            'savings_badge' => 'Save 15%', 'ribbon' => 'MOST POPULAR', 'highlighted' => 1, 'display_order' => 4,
            'priority_rank' => 4,
            'contacts_limit' => 200, 'interests_per_month' => 0, 'shortlist_limit' => 0,
            'boosts_per_month' => 2, 'featured_days' => 30, 'always_featured' => 0,
            'advanced_search' => 1, 'see_who_viewed' => 1, 'see_who_shortlisted' => 1,
            'unlimited_photos' => 1, 'unlimited_search' => 1, 'premium_badge' => 1,
            'match_suggestions' => 'Twice Weekly', 'support_tier' => 'Faster Priority',
            'features' => "Everything in Divine Plus PLUS\nView 200 Contact Details\nHigh Search Priority\nFeatured Profile for 30 Days\nTwo Profile Boosts every Month\nPersonalized Match Recommendations\nFaster Priority Support\nPremium Membership Badge",
        ],
        [
            'slug' => 'eternal', 'name' => 'Eternal Premium', 'tagline' => 'Best value — a full year of everything',
            'price' => 2999, 'monthly_display' => 250, 'duration_days' => 365, 'duration_months' => 12,
            'savings_badge' => 'Save 28%', 'ribbon' => 'BEST VALUE', 'highlighted' => 0, 'display_order' => 5,
            'priority_rank' => 5,
            'contacts_limit' => 0, 'interests_per_month' => 0, 'shortlist_limit' => 0,
            'boosts_per_month' => 4, 'featured_days' => 0, 'always_featured' => 1,
            'advanced_search' => 1, 'see_who_viewed' => 1, 'see_who_shortlisted' => 1,
            'unlimited_photos' => 1, 'unlimited_search' => 1, 'premium_badge' => 1,
            'match_suggestions' => 'Personalized', 'support_tier' => 'Dedicated',
            'features' => "Everything in Soul Elite PLUS\nUnlimited Contact Details\nHighest Search Priority\nAlways Featured while Membership Active\nWeekly Profile Boost\nDedicated Premium Support\nPersonalized AI Match Suggestions\nEarly Access to New Features",
        ],
    ];
    foreach ($planSeed as $p) {
        // Preserve admin price/feature edits after the canonical plans exist.
        // Only legacy pre-slug rows, or totally missing canonical rows, get seeded.
        $existingId = DB::val('SELECT id FROM packages WHERE slug = ? LIMIT 1', [$p['slug']]);
        $row = [
            'slug'                => $p['slug'],
            'name'                => $p['name'],
            'tagline'             => $p['tagline'],
            'price'               => $p['price'],
            'currency'            => 'INR',
            'duration_days'       => $p['duration_days'],
            'duration_months'     => $p['duration_months'],
            'monthly_display'     => $p['monthly_display'],
            'savings_badge'       => $p['savings_badge'],
            'ribbon'              => $p['ribbon'],
            'priority_rank'       => $p['priority_rank'],
            'contacts_limit'      => $p['contacts_limit'],
            'interests_per_month' => $p['interests_per_month'],
            'shortlist_limit'     => $p['shortlist_limit'],
            'boosts_per_month'    => $p['boosts_per_month'],
            'featured_days'       => $p['featured_days'],
            'always_featured'     => $p['always_featured'],
            'advanced_search'     => $p['advanced_search'],
            'see_who_viewed'      => $p['see_who_viewed'],
            'see_who_shortlisted' => $p['see_who_shortlisted'],
            'unlimited_photos'    => $p['unlimited_photos'],
            'unlimited_search'    => $p['unlimited_search'],
            'premium_badge'       => $p['premium_badge'],
            'match_suggestions'   => $p['match_suggestions'],
            'support_tier'        => $p['support_tier'],
            'features'            => $p['features'],
            'highlighted'         => $p['highlighted'],
            'is_active'           => 1,
            'display_order'       => $p['display_order'],
        ];
        try {
            if ($existingId) {
                continue;
            }
            $legacyId = DB::val('SELECT id FROM packages WHERE slug IS NULL AND LOWER(name) = ? LIMIT 1', [strtolower($p['name'])]);
            if ($legacyId) {
                DB::update('packages', $row, ['id' => $legacyId]);
            } else {
                DB::insert('packages', $row);
            }
        } catch (Throwable $e) { /* seed best-effort */ }
    }
    // Retire any leftover packages that aren't part of the canonical five (keeps the /packages page tidy on upgrades).
    try {
        DB::q("UPDATE packages SET is_active = 0 WHERE slug IS NULL OR slug NOT IN ('free','starter','divine','soul_elite','eternal')");
    } catch (Throwable $e) { /* safe */ }

    // Add-on catalogue seed.
    $addonSeed = [
        ['slug' => 'boost_7',     'name' => 'Profile Boost (7 Days)',            'description' => 'Higher search ranking for a full week',            'price' => 99,  'kind' => 'boost',        'duration_days' => 7,  'quantity' => 0, 'display_order' => 1],
        ['slug' => 'spotlight_1', 'name' => 'Spotlight Profile (24 Hours)',      'description' => 'Top of search results for 24 hours',               'price' => 149, 'kind' => 'spotlight',    'duration_days' => 1,  'quantity' => 0, 'display_order' => 2],
        ['slug' => 'featured_15', 'name' => 'Featured Profile (15 Days)',        'description' => '15 days of featured placement on the home page',   'price' => 199, 'kind' => 'featured',     'duration_days' => 15, 'quantity' => 0, 'display_order' => 3],
        ['slug' => 'contacts_25', 'name' => 'Extra Contact Pack (25 Contacts)',  'description' => '25 additional contact detail unlocks',             'price' => 199, 'kind' => 'contact_pack', 'duration_days' => 0,  'quantity' => 25, 'display_order' => 4],
        ['slug' => 'review',      'name' => 'Profile Review',                    'description' => 'One-on-one review of your profile by our team',    'price' => 299, 'kind' => 'review',       'duration_days' => 0,  'quantity' => 1, 'display_order' => 5],
    ];
    foreach ($addonSeed as $a) {
        $existingId = DB::val('SELECT id FROM addons WHERE slug = ? LIMIT 1', [$a['slug']]);
        $row = $a + ['currency' => 'INR', 'is_active' => 1];
        try {
            if (!$existingId) DB::insert('addons', $row);
        } catch (Throwable $e) { /* safe */ }
    }

    // Verification pricing exposed as settings so admin can tune them without a DB change.
    foreach ([
        'verify_identity_price'      => '299',
        'verify_selfie_price'        => '499',
    ] as $k => $v) {
        DB::q("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)", [$k, $v]);
    }

    // Auto-expire subscriptions whose ends_at has passed. Runs cheaply once per request
    // and keeps the "downgrade to Free" behaviour honest without needing a cron.
    try {
        DB::q("UPDATE subscriptions SET status = 'expired' WHERE status = 'active' AND ends_at < NOW()");
        DB::q("UPDATE addon_purchases SET status = 'expired' WHERE status = 'active' AND ends_at IS NOT NULL AND ends_at < NOW()");
    } catch (Throwable $e) { /* safe */ }

} catch (Throwable $e) { /* harmless on first install */ }

csrf_check();

$r = new Router();
require __DIR__ . '/../app/routes.php';
require __DIR__ . '/../app/admin_routes.php';

$r->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
