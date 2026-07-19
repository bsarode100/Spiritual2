<?php
// Application configuration. Reads from environment (or .env).
// Designed to work in both Coolify (env vars injected) and local docker-compose.

return [
    'app' => [
        'name'     => getenv('APP_NAME')     ?: 'Spiritual Matrimony',
        'url'      => getenv('APP_URL')      ?: 'http://localhost:8080',
        'env'      => getenv('APP_ENV')      ?: 'production',
        'debug'    => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN),
        'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Kolkata',
        'key'      => getenv('APP_KEY')      ?: 'change-me-in-production',
    ],
    'db' => [
        'host'     => getenv('DB_HOST') ?: 'db',
        'port'     => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'spiritual',
        'username' => getenv('DB_USERNAME') ?: 'spiritual',
        'password' => getenv('DB_PASSWORD') ?: 'spiritual',
        'charset'  => 'utf8mb4',
    ],
    'mail' => [
        'mailer'     => getenv('MAIL_MAILER') ?: 'mail',
        'host'       => getenv('MAIL_HOST') ?: '',
        'port'       => (int)(getenv('MAIL_PORT') ?: 587),
        'username'   => getenv('MAIL_USERNAME') ?: '',
        'password'   => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
        'from'       => getenv('MAIL_FROM_ADDRESS') ?: '',
        'from_name'  => getenv('MAIL_FROM_NAME') ?: (getenv('APP_NAME') ?: 'Spiritual Matrimony'),
        'timeout'    => (int)(getenv('MAIL_TIMEOUT') ?: 15),
    ],
    'uploads' => [
        'avatar_dir' => __DIR__ . '/../public/uploads/avatars',
        'blog_dir'   => __DIR__ . '/../public/uploads/blog',
        'site_dir'   => __DIR__ . '/../public/uploads/site',
        // Verification documents (govt IDs, live selfies) hold PII — kept OUTSIDE
        // public/ and only ever streamed through authenticated routes, never a URL.
        'verify_dir' => __DIR__ . '/../storage/verification',
        'max_bytes'  => 4 * 1024 * 1024, // 4 MB
        'allowed'    => ['jpg','jpeg','png','webp'],
        // Live selfie video from getUserMedia (webm) or a phone camera capture (mp4)
        'verify_video_max_bytes' => 15 * 1024 * 1024, // 15 MB ≈ 10s clip
    ],
];
