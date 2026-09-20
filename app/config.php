<?php
// Application configuration. Reads from environment (or .env).
// Designed to work in both Coolify (env vars injected) and local docker-compose.

$env = function(string $key, $default = null) {
    $val = getenv($key);
    if ($val !== false && $val !== '') return $val;
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
    return $default;
};

return [
    'app' => [
        'name'     => $env('APP_NAME', 'SpiritualShaadi'),
        'url'      => $env('APP_URL', 'http://localhost:8080'),
        'env'      => $env('APP_ENV', 'production'),
        'debug'    => filter_var($env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
        'timezone' => $env('APP_TIMEZONE', 'Asia/Kolkata'),
        'key'      => $env('APP_KEY', 'change-me-in-production'),
    ],
    'db' => [
        'host'     => $env('DB_HOST', 'db'),
        'port'     => $env('DB_PORT', '3306'),
        'database' => $env('DB_DATABASE', 'spiritual'),
        'username' => $env('DB_USERNAME', 'spiritual'),
        'password' => $env('DB_PASSWORD', 'spiritual'),
        'charset'  => 'utf8mb4',
    ],
    'mail' => [
        'mailer'     => $env('MAIL_MAILER', 'mail'),
        'host'       => $env('MAIL_HOST', ''),
        'port'       => (int) $env('MAIL_PORT', 587),
        'username'   => $env('MAIL_USERNAME', ''),
        'password'   => $env('MAIL_PASSWORD', ''),
        'encryption' => $env('MAIL_ENCRYPTION', 'tls'),
        'from'       => $env('MAIL_FROM_ADDRESS', ''),
        'from_name'  => $env('MAIL_FROM_NAME', $env('APP_NAME', 'SpiritualShaadi')),
        'timeout'    => (int) $env('MAIL_TIMEOUT', 15),
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
