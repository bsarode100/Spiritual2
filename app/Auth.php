<?php
// Session-backed auth helper.
class Auth {
    public static function user(): ?array {
        if (empty($_SESSION['uid'])) return null;
        static $cache = null;
        if ($cache && $cache['id'] == $_SESSION['uid']) return $cache;
        $cache = DB::one('SELECT * FROM users WHERE id = ?', [$_SESSION['uid']]);
        return $cache;
    }

    public static function check(): bool { return !empty($_SESSION['uid']); }

    public static function id(): ?int { return $_SESSION['uid'] ?? null; }

    public static function isAdmin(): bool {
        $u = self::user();
        return $u && $u['role'] === 'admin';
    }

    public static function login(int $id): void {
        session_regenerate_id(true);
        $_SESSION['uid'] = $id;
        DB::q('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$id]);
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function require(): void {
        if (!self::check()) {
            $_SESSION['flash_error'] = 'Please sign in to continue.';
            redirect('/login');
        }
    }

    public static function requireAdmin(): void {
        if (!self::isAdmin()) {
            http_response_code(403);
            echo "Forbidden — admin only.";
            exit;
        }
    }
}
