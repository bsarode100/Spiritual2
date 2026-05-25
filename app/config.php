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
    'uploads' => [
        'avatar_dir' => __DIR__ . '/../public/uploads/avatars',
        'blog_dir'   => __DIR__ . '/../public/uploads/blog',
        'site_dir'   => __DIR__ . '/../public/uploads/site',
        'max_bytes'  => 4 * 1024 * 1024, // 4 MB
        'allowed'    => ['jpg','jpeg','png','webp'],
    ],
];
