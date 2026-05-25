<?php
// View renderer + global helpers.

function view(string $template, array $data = [], ?string $layout = 'main'): void {
    extract($data, EXTR_SKIP);
    $viewFile = __DIR__ . '/../views/' . $template . '.php';
    if (!is_file($viewFile)) {
        http_response_code(500);
        echo "View not found: $template";
        return;
    }
    ob_start();
    include $viewFile;
    $content = ob_get_clean();
    if ($layout === null) {
        echo $content;
        return;
    }
    $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';
    include $layoutFile;
}

function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function url(string $path = '/'): string {
    return rtrim(($GLOBALS['CFG']['app']['url'] ?? ''), '/') . $path;
}

function asset(string $path): string {
    return '/assets/' . ltrim($path, '/');
}

function upload_url(string $path): string {
    if (!$path) return '';
    if (str_starts_with($path, 'http')) return $path;
    return '/uploads/' . ltrim($path, '/');
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function flash(string $key, ?string $msg = null): ?string {
    if ($msg !== null) {
        $_SESSION['flash_' . $key] = $msg;
        return null;
    }
    $val = $_SESSION['flash_' . $key] ?? null;
    unset($_SESSION['flash_' . $key]);
    return $val;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? '')) {
        http_response_code(419);
        echo 'CSRF token mismatch. <a href="/">Go home</a>.';
        exit;
    }
}

function setting(string $key, ?string $default = null): ?string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (DB::all('SELECT setting_key, setting_value FROM site_settings') as $r) {
            $cache[$r['setting_key']] = $r['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

function age_from_dob(?string $dob): ?int {
    if (!$dob) return null;
    try {
        $d = new DateTime($dob);
        return $d->diff(new DateTime('today'))->y;
    } catch (Throwable) { return null; }
}

function cm_to_feet(?int $cm): ?string {
    if (!$cm) return null;
    $inches = round($cm / 2.54);
    $ft = intdiv($inches, 12);
    $in = $inches % 12;
    return "{$ft}' {$in}\"";
}

function primary_photo(int $userId): ?string {
    $p = DB::val('SELECT path FROM photos WHERE user_id = ? ORDER BY is_primary DESC, id ASC LIMIT 1', [$userId]);
    return $p ?: null;
}

function avatar_url(array $user): string {
    $p = primary_photo((int)$user['id']);
    if ($p) return upload_url($p);
    $seed = urlencode($user['name'] ?? 'Seeker');
    return "https://api.dicebear.com/9.x/initials/svg?seed={$seed}&backgroundColor=B8860B,8B2C2C,2D1B4E&textColor=FAF3E0";
}

function slugify(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('~[^a-z0-9]+~', '-', $s) ?? $s;
    return trim($s, '-');
}

function paginate(int $total, int $perPage, int $page): array {
    $pages = max(1, (int) ceil($total / $perPage));
    $page  = max(1, min($pages, $page));
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'limit' => $perPage];
}

function active(string $needle, string $haystack, string $cls = 'active'): string {
    return str_contains($haystack, $needle) ? $cls : '';
}

function nav_active(string $path): string {
    $cur = $_SERVER['REQUEST_URI'] ?? '/';
    if ($path === '/') {
        return ($cur === '/' || $cur === '') ? 'is-active' : '';
    }
    return str_starts_with($cur, $path) ? 'is-active' : '';
}
