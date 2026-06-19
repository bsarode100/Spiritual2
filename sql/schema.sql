-- =====================================================================
-- Spiritual Matrimony - Database Schema
-- MySQL 8 / MariaDB 10.5+
-- =====================================================================
SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ---------- USERS ----------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(120) NOT NULL,
  `email`          VARCHAR(190) NOT NULL,
  `phone`          VARCHAR(30)  DEFAULT NULL,
  `password_hash`  VARCHAR(255) NOT NULL,
  `role`           ENUM('member','admin') NOT NULL DEFAULT 'member',
  `status`         ENUM('active','pending','blocked') NOT NULL DEFAULT 'active',
  `last_login_at`  DATETIME DEFAULT NULL,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- PROFILES (matrimony bio-data) ----------
DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`          BIGINT UNSIGNED NOT NULL,
  `gender`           ENUM('male','female') NOT NULL,
  `dob`              DATE NOT NULL,
  `height_cm`        SMALLINT UNSIGNED DEFAULT NULL,
  `marital_status`   ENUM('never_married','divorced','widowed','separated') NOT NULL DEFAULT 'never_married',
  `mother_tongue`    VARCHAR(60)  DEFAULT NULL,
  `religion`         VARCHAR(60)  DEFAULT 'Hindu',
  `community`        VARCHAR(80)  DEFAULT NULL,
  `caste`            VARCHAR(80)  DEFAULT NULL,
  `gotra`            VARCHAR(80)  DEFAULT NULL,
  `manglik`          ENUM('yes','no','dont_know') DEFAULT 'dont_know',
  `country`          VARCHAR(60)  DEFAULT 'India',
  `state`            VARCHAR(80)  DEFAULT NULL,
  `city`             VARCHAR(80)  DEFAULT NULL,
  `education`        VARCHAR(150) DEFAULT NULL,
  `profession`       VARCHAR(150) DEFAULT NULL,
  `annual_income`    VARCHAR(60)  DEFAULT NULL,
  `family_type`      ENUM('nuclear','joint','other') DEFAULT 'nuclear',
  `family_status`    ENUM('middle_class','upper_middle','affluent','rich') DEFAULT 'middle_class',
  `diet`             ENUM('vegetarian','vegan','sattvic','eggetarian','non_vegetarian','jain') DEFAULT 'vegetarian',
  `about_me`         TEXT,
  `partner_pref`     TEXT,
  `profile_complete` TINYINT(1) NOT NULL DEFAULT 0,
  `views`            INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profiles_user_id_unique` (`user_id`),
  CONSTRAINT `profiles_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- SPIRITUAL DETAILS ----------
DROP TABLE IF EXISTS `spiritual_details`;
CREATE TABLE `spiritual_details` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         BIGINT UNSIGNED NOT NULL,
  `spiritual_path`  VARCHAR(120) DEFAULT NULL,          -- ISKCON, Art of Living, Vipassana, Sahaja Yoga, Brahmakumaris, etc.
  `guru`            VARCHAR(120) DEFAULT NULL,
  `ishta_devata`    VARCHAR(120) DEFAULT NULL,
  `daily_sadhana`   VARCHAR(200) DEFAULT NULL,          -- e.g. "108 mala japa, 1hr meditation"
  `favorite_scripture` VARCHAR(120) DEFAULT NULL,       -- Bhagavad Gita, Yoga Sutras, etc.
  `fasting_practice`   VARCHAR(120) DEFAULT NULL,       -- Ekadashi, Mondays...
  `pilgrimage_done`    TEXT,
  `mantra`             VARCHAR(255) DEFAULT NULL,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spiritual_user_unique` (`user_id`),
  CONSTRAINT `spiritual_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- HOROSCOPE ----------
DROP TABLE IF EXISTS `horoscopes`;
CREATE TABLE `horoscopes` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         BIGINT UNSIGNED NOT NULL,
  `time_of_birth`   VARCHAR(10) DEFAULT NULL,
  `place_of_birth`  VARCHAR(150) DEFAULT NULL,
  `rashi`           VARCHAR(60) DEFAULT NULL,
  `nakshatra`       VARCHAR(60) DEFAULT NULL,
  `gotra`           VARCHAR(60) DEFAULT NULL,
  `chart_image`     VARCHAR(255) DEFAULT NULL,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `horoscopes_user_unique` (`user_id`),
  CONSTRAINT `horoscope_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- PHOTOS ----------
DROP TABLE IF EXISTS `photos`;
CREATE TABLE `photos` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `path`       VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `photos_user_idx` (`user_id`),
  CONSTRAINT `photos_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- INTERESTS ----------
DROP TABLE IF EXISTS `interests`;
CREATE TABLE `interests` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id`   BIGINT UNSIGNED NOT NULL,
  `receiver_id` BIGINT UNSIGNED NOT NULL,
  `status`      ENUM('sent','accepted','declined','cancelled') NOT NULL DEFAULT 'sent',
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `interests_pair_unique` (`sender_id`,`receiver_id`),
  KEY `interests_receiver_idx` (`receiver_id`),
  CONSTRAINT `interests_sender_fk`   FOREIGN KEY (`sender_id`)   REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interests_receiver_fk` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- SHORTLIST ----------
DROP TABLE IF EXISTS `shortlists`;
CREATE TABLE `shortlists` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       BIGINT UNSIGNED NOT NULL,
  `target_user_id`BIGINT UNSIGNED NOT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortlists_pair_unique` (`user_id`,`target_user_id`),
  CONSTRAINT `shortlists_user_fk`   FOREIGN KEY (`user_id`)        REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shortlists_target_fk` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- MESSAGES ----------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id`   BIGINT UNSIGNED NOT NULL,
  `receiver_id` BIGINT UNSIGNED NOT NULL,
  `body`        TEXT NOT NULL,
  `read_at`     DATETIME DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `messages_thread_idx` (`sender_id`,`receiver_id`,`created_at`),
  CONSTRAINT `messages_sender_fk`   FOREIGN KEY (`sender_id`)   REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_receiver_fk` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- PACKAGES ----------
DROP TABLE IF EXISTS `packages`;
CREATE TABLE `packages` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(80)  NOT NULL,
  `tagline`      VARCHAR(160) DEFAULT NULL,
  `price`        DECIMAL(10,2) NOT NULL DEFAULT 0,
  `currency`     VARCHAR(8)   NOT NULL DEFAULT 'INR',
  `duration_days`SMALLINT UNSIGNED NOT NULL DEFAULT 90,
  `contacts_limit` SMALLINT UNSIGNED DEFAULT 0,        -- 0 = unlimited
  `features`     TEXT,                                  -- newline-separated list
  `highlighted`  TINYINT(1) NOT NULL DEFAULT 0,
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  `display_order`SMALLINT NOT NULL DEFAULT 0,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- SUBSCRIPTIONS ----------
DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `package_id` BIGINT UNSIGNED NOT NULL,
  `starts_at`  DATETIME NOT NULL,
  `ends_at`    DATETIME NOT NULL,
  `status`     ENUM('pending','active','expired','cancelled') NOT NULL DEFAULT 'pending',
  `payment_ref`VARCHAR(120) DEFAULT NULL,
  `amount`     DECIMAL(10,2) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `subs_user_idx` (`user_id`),
  KEY `subs_pkg_idx`  (`package_id`),
  CONSTRAINT `subs_user_fk` FOREIGN KEY (`user_id`)    REFERENCES `users`    (`id`) ON DELETE CASCADE,
  CONSTRAINT `subs_pkg_fk`  FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- BLOG ----------
DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(200) NOT NULL,
  `slug`          VARCHAR(220) NOT NULL,
  `excerpt`       VARCHAR(500) DEFAULT NULL,
  `body`          LONGTEXT,
  `cover_image`   VARCHAR(255) DEFAULT NULL,
  `category`      VARCHAR(80)  DEFAULT 'General',
  `author_name`   VARCHAR(120) DEFAULT NULL,
  `published`     TINYINT(1) NOT NULL DEFAULT 1,
  `published_at`  DATETIME DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- CMS PAGES ----------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`       VARCHAR(120) NOT NULL,
  `title`      VARCHAR(200) NOT NULL,
  `body`       LONGTEXT,
  `published`  TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- HAPPY STORIES (TESTIMONIALS) ----------
DROP TABLE IF EXISTS `happy_stories`;
CREATE TABLE `happy_stories` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `couple_name`  VARCHAR(160) NOT NULL,
  `story`        TEXT NOT NULL,
  `photo`        VARCHAR(255) DEFAULT NULL,
  `married_on`   DATE DEFAULT NULL,
  `is_featured`  TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- SITE SETTINGS (key/value singletons) ----------
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `setting_key`   VARCHAR(80) NOT NULL,
  `setting_value` TEXT,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- PASSWORD RESETS ----------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED NOT NULL,
  `otp_hash`     CHAR(64) NOT NULL,
  `attempts`     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at`   DATETIME NOT NULL,
  `used_at`      DATETIME DEFAULT NULL,
  `requested_ip` VARCHAR(45) DEFAULT NULL,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `password_resets_user_idx` (`user_id`),
  KEY `password_resets_active_idx` (`user_id`, `used_at`, `expires_at`),
  CONSTRAINT `password_resets_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- PAYMENTS (Razorpay) ----------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
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
  KEY `payments_order_idx` (`gateway_order_id`),
  CONSTRAINT `payments_user_fk` FOREIGN KEY (`user_id`)    REFERENCES `users`    (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_pkg_fk`  FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- CONTACT MESSAGES ----------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(190) NOT NULL,
  `phone`      VARCHAR(40)  DEFAULT NULL,
  `subject`    VARCHAR(200) DEFAULT NULL,
  `message`    TEXT NOT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- SEED DATA
-- =====================================================================

-- Default admin (password: admin@123)
INSERT INTO `users` (`name`,`email`,`password_hash`,`role`,`status`) VALUES
('Site Administrator','admin@spiritual2.test','$2y$10$Q3F7p1bA0w3z/lQz2Yk6euJv0Ozp0u0YQbpV0aJk9XjV0wEPjxw7K','admin','active');

-- Sample members (password: member@123)  hash placeholder will be overwritten by installer
INSERT INTO `users` (`name`,`email`,`password_hash`,`role`,`status`) VALUES
('Anjali Sharma','anjali@example.com','$2y$10$Q3F7p1bA0w3z/lQz2Yk6euJv0Ozp0u0YQbpV0aJk9XjV0wEPjxw7K','member','active'),
('Rohan Iyer','rohan@example.com','$2y$10$Q3F7p1bA0w3z/lQz2Yk6euJv0Ozp0u0YQbpV0aJk9XjV0wEPjxw7K','member','active'),
('Meera Krishnan','meera@example.com','$2y$10$Q3F7p1bA0w3z/lQz2Yk6euJv0Ozp0u0YQbpV0aJk9XjV0wEPjxw7K','member','active'),
('Aditya Bhatt','aditya@example.com','$2y$10$Q3F7p1bA0w3z/lQz2Yk6euJv0Ozp0u0YQbpV0aJk9XjV0wEPjxw7K','member','active'),
('Radhika Joshi','radhika@example.com','$2y$10$Q3F7p1bA0w3z/lQz2Yk6euJv0Ozp0u0YQbpV0aJk9XjV0wEPjxw7K','member','active'),
('Karthik Menon','karthik@example.com','$2y$10$Q3F7p1bA0w3z/lQz2Yk6euJv0Ozp0u0YQbpV0aJk9XjV0wEPjxw7K','member','active');

INSERT INTO `profiles` (`user_id`,`gender`,`dob`,`height_cm`,`mother_tongue`,`religion`,`community`,`country`,`state`,`city`,`education`,`profession`,`annual_income`,`diet`,`about_me`,`partner_pref`,`profile_complete`) VALUES
(2,'female','1996-05-12',162,'Hindi','Hindu','Brahmin','India','Maharashtra','Pune','MA Yoga Sciences','Yoga Teacher','5-8 LPA','sattvic','A seeker on the path of bhakti. I begin every day with sadhana and find joy in seva. Looking for a partner who walks the spiritual path with sincerity.','A spiritually inclined, kind partner who values dharma and family.',1),
(3,'male','1992-09-23',178,'Tamil','Hindu','Iyer','India','Karnataka','Bengaluru','B.Tech, IIT Madras','Software Engineer','25-35 LPA','vegetarian','Engineer by day, sadhak by dawn. Practicing Vipassana for 6 years. Deeply rooted in Sanatana Dharma.','A grounded, compassionate woman who values inner growth.',1),
(4,'female','1994-02-08',165,'Tamil','Hindu','Iyengar','India','Tamil Nadu','Chennai','PhD Sanskrit','Professor','12-15 LPA','sattvic','Lifelong learner of the Vedas. Daily Gayatri sadhaka. Music is my meditation.','Someone equally curious about life and dharma.',1),
(5,'male','1990-11-30',182,'Gujarati','Hindu','Vaishnav','India','Gujarat','Ahmedabad','MBA, IIM-A','Entrepreneur','50+ LPA','vegetarian','Born into a devout ISKCON family. Run a sustainable agriculture venture. Bhakti and business, walking hand in hand.','A devotee at heart with grace in everyday life.',1),
(6,'female','1995-07-19',160,'Hindi','Hindu','Agarwal','India','Delhi','New Delhi','M.A. Psychology','Counselor','8-12 LPA','vegetarian','Trained in Vipassana and Sahaja Yoga. Help people heal through mindful conversation.','A patient, self-aware partner who values silence and depth.',1),
(7,'male','1988-03-04',175,'Malayalam','Hindu','Nair','India','Kerala','Kochi','MBBS, AIIMS','Ayurvedic Doctor','15-20 LPA','sattvic','Practicing Ayurveda and classical yoga. Disciple of an Advaita Vedanta teacher. Simple living, high thinking.','A truthful, grounded life-companion.',1);

INSERT INTO `spiritual_details` (`user_id`,`spiritual_path`,`guru`,`ishta_devata`,`daily_sadhana`,`favorite_scripture`,`mantra`) VALUES
(2,'Bhakti Yoga','Mata Amritanandamayi','Krishna','108 mala japa, 1hr kirtan','Bhagavad Gita','Hare Krishna Maha Mantra'),
(3,'Vipassana','S. N. Goenka','—','1hr Anapana + 1hr Vipassana, daily','Dhammapada','Anicca Anicca'),
(4,'Sri Vidya','—','Lalita Tripura Sundari','Sandhya Vandana, Lalita Sahasranama','Lalita Sahasranama','Sri Matre Namah'),
(5,'ISKCON','Srila Prabhupada','Radha-Krishna','16 rounds Maha Mantra','Srimad Bhagavatam','Hare Krishna'),
(6,'Sahaja Yoga','Shri Mataji Nirmala Devi','Adi Shakti','Morning + evening meditation','Devi Mahatmya','Aum Twameva Sakshat'),
(7,'Advaita Vedanta','Swami Dayananda Saraswati','Dakshinamurthy','Nitya pooja, Upanishad svadhyaya','Mandukya Upanishad','Aum');

-- Default site settings
INSERT INTO `site_settings` (`setting_key`,`setting_value`) VALUES
('site_name','Spiritual Matrimony'),
('site_tagline','Where two souls meet on the same path'),
('hero_heading','Find a partner who walks your spiritual path'),
('hero_subheading','A sacred space for sincere seekers to find a life-companion rooted in dharma, sadhana, and love.'),
('hero_cta_text','Begin Your Journey'),
('about_short','Spiritual Matrimony is a curated community for sincere seekers — devotees, sadhakas, yogis, and dharmics — looking for a life partner aligned with their spiritual journey.'),
('contact_email','hello@spiritualmatrimony.com'),
('contact_phone','+91 98XXX XXXXX'),
('contact_address','Rishikesh, Uttarakhand, India'),
('social_facebook','https://facebook.com/'),
('social_instagram','https://instagram.com/'),
('social_youtube','https://youtube.com/'),
('footer_about','Two souls. One path. A lifetime of sadhana — together.'),
('stat_members','25,000+'),
('stat_marriages','1,200+'),
('stat_paths','18'),
('stat_countries','40+'),
-- Payment details (editable from /admin/payment-details). Leave blank to hide a row on the public page.
('payment_payee_name','Spiritual Matrimony'),
('payment_upi_id',''),
('payment_upi_qr_url',''),
('payment_bank_name',''),
('payment_account_name',''),
('payment_account_number',''),
('payment_ifsc',''),
('payment_branch',''),
('payment_contact_phone',''),
('payment_contact_email','hello@spiritualmatrimony.com'),
('payment_instructions','After completing the payment via UPI or bank transfer, share the transaction reference and your registered email with our support team. Your premium plan will be activated within 24 hours.'),
-- Razorpay gateway (editable from /admin/razorpay). Keep `razorpay_enabled` = 0 until you've pasted real keys.
('razorpay_enabled','0'),
('razorpay_mode','test'),
('razorpay_key_id',''),
('razorpay_key_secret',''),
('razorpay_webhook_secret','');

-- Default packages
INSERT INTO `packages` (`name`,`tagline`,`price`,`currency`,`duration_days`,`contacts_limit`,`features`,`highlighted`,`display_order`) VALUES
('Sadhak','For the sincere seeker',0,'INR',365,5,'View 5 detailed profiles\nSend 5 interests\nBasic search filters\nView shared photos','0',1),
('Sankalp','Most popular — for serious seekers',2999,'INR',180,50,'View 50 detailed profiles\nSend unlimited interests\nAdvanced search & spiritual filters\nDirect chat with matches\nHoroscope download\nProfile highlighted in search','1',2),
('Sangam','Premium concierge journey',9999,'INR',365,0,'Unlimited everything\nPersonal matchmaker call\nProfile written by our team\nProfessional photoshoot guidance\nPriority verification\nFeatured in newsletter','0',3);

-- Sample happy stories
INSERT INTO `happy_stories` (`couple_name`,`story`,`is_featured`,`married_on`) VALUES
('Krishna & Radha','We met on Spiritual Matrimony in 2024 over a shared love of kirtan. Our first conversation lasted 4 hours. Today we host kirtan evenings together in Vrindavan.',1,'2025-02-14'),
('Arjun & Aanya','As Vipassana sadhaks we wanted a partner who understood silence. Spiritual Matrimony understood. We were married on a quiet morning in Igatpuri.',1,'2024-12-05'),
('Vikram & Sita','Our families had given up matchmaking — too "different" they said. Then Sankalp Plan found us each other in three weeks. Three weeks!',0,'2024-09-22');

-- Sample blog
INSERT INTO `blog_posts` (`title`,`slug`,`excerpt`,`body`,`category`,`author_name`,`published`,`published_at`) VALUES
('What Bhagavad Gita teaches us about choosing a life partner','gita-life-partner','Krishna''s counsel to Arjuna on action, attachment, and dharma holds quiet wisdom for the most important decision of our householder life.','<p>In the Bhagavad Gita, Krishna does not speak of marriage directly — and yet every shloka about dharma, swabhava, and karma yoga applies to the choice of a life-partner more than perhaps any other decision we make...</p><p>Choose someone whose dharma harmonises with yours, not merely whose stars do. Choose stillness over sparkle, sangha over status, and you will have chosen the Gita''s way.</p>','Wisdom','Acharya Vidyananda',1,NOW()),
('Five questions to ask before your first sankalpa together','five-questions-sankalpa','Before the wedding mantras, before the saptapadi — there are five questions every spiritual couple should sit with.','<p>1. <strong>Whose feet do you bow to?</strong> A shared lineage of gurus or paths is a quiet but tremendous gift...</p><p>2. <strong>What does a Sunday morning look like in our home?</strong> Sadhana style reveals more than salary slips...</p>','Guidance','Mata Saraswati Devi',1,NOW()),
('Vegetarian, Sattvic, or Vegan — does it really matter in a marriage?','sattvic-marriage-diet','The food on our plate becomes the thoughts in our mind. When two souls share a kitchen, that becomes very real, very fast.','<p>The yogi knows: anna becomes manas. Food becomes mind. So when two souls promise to share a hearth for a lifetime...</p>','Lifestyle','Yogi Hridayananda',1,NOW());

-- CMS pages
-- NOTE: privacy, terms, refund-policy and cookie-policy are seeded here only as
-- short placeholders so the rows exist immediately. The full, India-compliance
-- aware long-form content lives in sql/pages_seed.sql and is loaded by the
-- auto-installer (public/index.php) right after schema.sql, using
-- INSERT ... ON DUPLICATE KEY UPDATE so admin edits are never silently re-run.
INSERT INTO `pages` (`slug`,`title`,`body`,`published`) VALUES
('about','About Us','<h2>Our Story</h2><p>Spiritual Matrimony was born from a simple observation: the most important question a sadhak can ask of a life-partner — "do you walk the same path?" — was rarely the first question on traditional matrimony sites.</p><p>We built this sacred space for sincere seekers. Devotees, yogis, dhyanis, dharmics — people for whom morning sadhana is not a hobby but a heartbeat.</p><h3>Our Values</h3><ul><li>Sincerity over sparkle</li><li>Sadhana over status</li><li>Family-friendly, dharma-rooted, technology-light</li></ul>',1),
('privacy','Privacy Policy','<p class="lead">Your privacy is sacred to us. This Privacy Policy explains how Spiritual Matrimony collects, uses and safeguards your personal information when you use our matrimonial services.</p><p><em>Effective from 1 January 2026.</em></p><h3>1. Information We Collect</h3><p>When you register and use our platform, we collect information you provide directly: your name, mobile number, email, date of birth, gender, marital status, height, religion, community, caste, profession, education, city/state/country, spiritual path, guru, ishta-devata, daily sadhana, dietary practices, "about me" notes, partner preferences, and uploaded photographs.</p><p>We also collect technical data automatically: device type, browser, IP address, pages visited, time spent and similar usage logs.</p><h3>2. How We Use Your Information</h3><ul><li>To create and operate your member profile</li><li>To match you with relevant prospective partners based on filters</li><li>To enable connection requests, shortlisting and private messaging</li><li>To process membership payments and subscription renewals</li><li>To send service notifications about interests received, matches, and important account updates</li><li>To prevent fraud, abuse and impersonation</li><li>To comply with applicable Indian law</li></ul><h3>3. Sharing of Information</h3><p>Profile fields visible to other registered members include your name, age, gender, city, profession, religion, community, spiritual path and "about me" — in line with your privacy preferences. Your mobile number and email are <strong>never</strong> shown directly; communication runs through our messaging system.</p><p>We do not sell, lease or trade your personal data. We may share information only with: payment gateways processing your transactions, hosting/email providers powering the site, and law-enforcement when strictly required by valid legal request.</p><h3>4. Cookies &amp; Tracking</h3><p>We use first-party cookies to keep you signed in and to remember your filter preferences. You can clear or disable cookies in your browser, but core features (login, messaging) may stop working.</p><h3>5. Data Retention</h3><p>We retain your account data for as long as your account is active. If you delete your account, we remove your personal profile data within a reasonable period, except where retention is required by law (for example, for payment and tax records).</p><h3>6. Security</h3><p>We protect your data with HTTPS in transit, hashed passwords at rest (bcrypt), role-based access controls and routine reviews. No method of internet transmission is 100% secure, and we cannot guarantee absolute security.</p><h3>7. Your Rights</h3><p>You can view, edit or download your personal information at any time from the profile section. You may also request deletion of your account by writing to our support team. You may withdraw consent to non-essential communications without affecting your membership.</p><h3>8. Children\'s Privacy</h3><p>Our services are not directed to individuals under 18. We do not knowingly collect personal information from minors and will remove any such data we discover.</p><h3>9. Changes to This Policy</h3><p>We may revise this policy from time to time. Material changes will be communicated through the platform, by email, or both. Continued use after the effective date of the revision indicates acceptance.</p><h3>10. Contact</h3><p>If you have questions about this policy or want to exercise your rights, please write to our support team using the contact details on the <a href="/contact">Contact</a> page.</p>',1),
('terms','Terms of Service','<p class="lead">Welcome to Spiritual Matrimony. By creating an account or using our services you agree to these Terms of Service. Please read them carefully.</p><p><em>Effective from 1 January 2026.</em></p><h3>1. Eligibility</h3><p>You must be at least 18 years of age and legally competent to enter into marriage under the laws applicable to you. Profiles created for any non-matrimonial purpose are strictly prohibited and will be removed without notice.</p><h3>2. Account &amp; Profile Information</h3><p>You are responsible for providing accurate, current and complete information during registration, and for keeping your password confidential. Misrepresentation of identity, age, marital status, religion or spiritual practice is grounds for immediate account termination and may invite legal action.</p><h3>3. Acceptable Use</h3><ul><li>Do not upload offensive, defamatory, sexually explicit or unlawful content.</li><li>Do not impersonate any other person or organisation.</li><li>Do not harass, threaten or repeatedly contact members who decline your interest.</li><li>Do not use the platform for solicitation, marketing, recruiting or any commercial activity outside our membership plans.</li><li>Do not scrape, copy or republish profile data.</li></ul><h3>4. Membership &amp; Payments</h3><p>Paid memberships unlock additional features as described on the <a href="/packages">Packages</a> page. All fees are quoted in Indian Rupees unless stated otherwise. Memberships activate after successful payment confirmation and are non-transferable. Payment details are listed on the <a href="/payment-details">Payment Details</a> page.</p><h3>5. Refunds</h3><p>Membership fees are non-refundable once a plan is activated, except where required by applicable consumer law. If you believe a charge was made in error or you experience a technical issue with activation, please contact support within 7 days of the transaction.</p><h3>6. Limitation of Liability</h3><p>Spiritual Matrimony acts only as a platform that connects prospective members. We do not verify the authenticity of every profile and we are not liable for the conduct of any member, the outcome of any introduction, or any loss arising from interactions initiated through the platform. Always exercise due diligence before sharing personal information or meeting in person.</p><h3>7. Termination</h3><p>We reserve the right to suspend or terminate accounts that violate these terms, harm other members, or pose a risk to the community — with or without prior notice. Fees paid for terminated accounts are not refundable.</p><h3>8. Intellectual Property</h3><p>All site content (logos, text, design, code) belongs to Spiritual Matrimony unless attributed otherwise. Member-generated content remains owned by the member, who grants us a limited licence to display it within the service.</p><h3>9. Changes to Terms</h3><p>We may update these terms from time to time. Continued use of the platform after such changes constitutes acceptance of the revised terms.</p><h3>10. Governing Law</h3><p>These terms are governed by the laws of India. Any dispute arising out of these terms shall be subject to the exclusive jurisdiction of the courts at the location of our registered office.</p><h3>11. Contact</h3><p>For any clarification regarding these terms, please reach out via the <a href="/contact">Contact</a> page.</p>',1),
('contact','Contact','<p>Write to us. We read every message.</p>',1),
('refund-policy','Refund & Cancellation Policy','<p>Loading… full Refund & Cancellation Policy is seeded from sql/pages_seed.sql by the auto-installer.</p>',1),
('cookie-policy','Cookie Policy','<p>Loading… full Cookie Policy is seeded from sql/pages_seed.sql by the auto-installer.</p>',1);
