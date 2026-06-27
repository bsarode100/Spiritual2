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
    // Webhook endpoints are authenticated via gateway HMAC signatures, not CSRF.
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $exempt = ['/razorpay/webhook', '/checkout/verify'];
    if (in_array($path, $exempt, true)) return;
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

function send_transactional_mail(string $to, string $subject, string $body, ?string $replyTo = null): bool {
    $cfg = $GLOBALS['CFG']['mail'] ?? [];
    $siteName = $GLOBALS['CFG']['app']['name'] ?? 'Spiritual Matrimony';
    $from = ($cfg['from'] ?? '') ?: setting('contact_email', 'no-reply@' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    $fromName = ($cfg['from_name'] ?? '') ?: $siteName;
    $replyTo = $replyTo ?: $from;
    $GLOBALS['last_mail_error'] = null;

    if (strtolower((string)($cfg['mailer'] ?? 'mail')) === 'smtp' && !empty($cfg['host'])) {
        try {
            return smtp_send_mail($cfg, $from, $fromName, $to, $subject, $body, $replyTo);
        } catch (Throwable $e) {
            $GLOBALS['last_mail_error'] = $e->getMessage();
            error_log('SMTP mail failed: ' . $e->getMessage());
            return false;
        }
    }

    $headers = [
        'From: ' . mail_address_header($from, $fromName),
        'Reply-To: ' . $replyTo,
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: ' . $siteName,
    ];
    $sent = @mail($to, $subject, $body, implode("\r\n", $headers));
    if (!$sent) {
        $GLOBALS['last_mail_error'] = 'PHP mail() returned false.';
    }
    return $sent;
}

function smtp_send_mail(array $cfg, string $from, string $fromName, string $to, string $subject, string $body, string $replyTo): bool {
    $host = (string) $cfg['host'];
    $port = (int) ($cfg['port'] ?? 587);
    $encryption = strtolower((string) ($cfg['encryption'] ?? 'tls'));
    $remote = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
    $timeout = (int) ($cfg['timeout'] ?? 15);

    $fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        throw new RuntimeException("Could not connect to SMTP server: {$errstr} ({$errno})");
    }
    stream_set_timeout($fp, $timeout);

    smtp_expect($fp, [220]);
    smtp_command($fp, 'EHLO ' . smtp_hostname(), [250]);

    if ($encryption === 'tls') {
        smtp_command($fp, 'STARTTLS', [220]);
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('Could not start SMTP TLS encryption.');
        }
        smtp_command($fp, 'EHLO ' . smtp_hostname(), [250]);
    }

    if (!empty($cfg['username'])) {
        smtp_command($fp, 'AUTH LOGIN', [334]);
        smtp_command($fp, base64_encode((string) $cfg['username']), [334]);
        smtp_command($fp, base64_encode((string) $cfg['password']), [235]);
    }

    smtp_command($fp, 'MAIL FROM:<' . $from . '>', [250]);
    smtp_command($fp, 'RCPT TO:<' . $to . '>', [250, 251]);
    smtp_command($fp, 'DATA', [354]);

    $headers = [
        'Date: ' . date(DATE_RFC2822),
        'From: ' . mail_address_header($from, $fromName),
        'To: <' . $to . '>',
        'Reply-To: ' . $replyTo,
        'Subject: ' . smtp_header_encode($subject),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    $message = implode("\r\n", $headers) . "\r\n\r\n" . str_replace(["\r\n", "\r"], "\n", $body);
    $message = str_replace("\n.", "\n..", $message);
    fwrite($fp, str_replace("\n", "\r\n", $message) . "\r\n.\r\n");
    smtp_expect($fp, [250]);
    smtp_command($fp, 'QUIT', [221]);
    fclose($fp);
    return true;
}

function smtp_command($fp, string $command, array $expected): string {
    fwrite($fp, $command . "\r\n");
    return smtp_expect($fp, $expected);
}

function smtp_expect($fp, array $expected): string {
    $response = '';
    while (($line = fgets($fp, 515)) !== false) {
        $response .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }
    $code = (int) substr($response, 0, 3);
    if (!in_array($code, $expected, true)) {
        throw new RuntimeException('Unexpected SMTP response: ' . trim($response));
    }
    return $response;
}

function smtp_hostname(): string {
    $host = $_SERVER['SERVER_NAME'] ?? 'localhost.localdomain';
    return preg_replace('/[^a-zA-Z0-9.-]/', '', $host) ?: 'localhost.localdomain';
}

function mail_address_header(string $email, string $name): string {
    $email = trim(str_replace(["\r", "\n"], '', $email));
    $name = trim(str_replace(['"', "\r", "\n"], '', $name));
    return $name !== '' ? '"' . $name . '" <' . $email . '>' : '<' . $email . '>';
}

function smtp_header_encode(string $value): string {
    return preg_match('/[^\x20-\x7E]/', $value)
        ? '=?UTF-8?B?' . base64_encode($value) . '?='
        : str_replace(["\r", "\n"], '', $value);
}
