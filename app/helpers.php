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
    // An empty stored value ("") means "not configured yet" from the admin's point of
    // view — falling through to the default keeps the footer copyright / contact chips
    // from rendering as "© 2026 ." or a blank mailto: link.
    $val = $cache[$key] ?? null;
    return ($val === null || $val === '') ? $default : $val;
}

// Renders a CMS page body. If the admin pasted structured HTML we trust it and pass
// through unchanged. Otherwise the body is plain text (or Markdown-ish paste with
// escape artifacts like "1\.") — we clean the escapes, wrap paragraphs, and promote
// short "N. Heading" lines to <h3> so the page has visible hierarchy instead of one
// wall of text.
function format_page_body(string $body): string {
    // A common admin mistake is pasting an entire exported HTML document — <!DOCTYPE>,
    // <html>, <head> and all — into the body field. The nested </body> then closes the
    // real page's body prematurely and pushes the site footer out of the render tree.
    // Unwrap to just the inner <body> content, and drop any embedded <style>/<script>
    // that could stomp the site's own CSS.
    if (preg_match('/<body\b[^>]*>(.*?)<\/body>/is', $body, $m)) {
        $body = $m[1];
    }
    $body = preg_replace('/<!DOCTYPE[^>]*>/i', '', $body) ?? $body;
    $body = preg_replace('/<\/?(?:html|head|body|meta|link|title)\b[^>]*>/i', '', $body) ?? $body;
    $body = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $body) ?? $body;
    $body = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $body) ?? $body;
    $body = trim($body);

    if (preg_match('/<(?:p|h[1-6]|ul|ol|li|div|table|section|article|blockquote)\b/i', $body)) {
        return $body;
    }
    $body = str_replace(["\r\n", "\r"], "\n", $body);
    // Strip Markdown-style backslash escapes on list/heading numerals.
    $body = preg_replace('/(\d+)\\\\\./', '$1.', $body);
    $paragraphs = preg_split('/\n{2,}/', trim($body));
    $out = [];
    foreach ($paragraphs as $para) {
        $para = trim($para);
        if ($para === '') continue;
        $lines = explode("\n", $para);

        // "1. Purpose of the Platform" as a single short line → treat as a section heading.
        if (count($lines) === 1 && preg_match('/^\d+\.\s+.{1,120}$/', $para)) {
            $out[] = '<h3>' . e($para) . '</h3>';
            continue;
        }

        // Bullet-style block ("- item" / "* item" on each line) → <ul>.
        $allBullets = true;
        foreach ($lines as $ln) {
            if (!preg_match('/^\s*[-*]\s+\S/', $ln)) { $allBullets = false; break; }
        }
        if ($allBullets) {
            $items = '';
            foreach ($lines as $ln) {
                $items .= '<li>' . e(preg_replace('/^\s*[-*]\s+/', '', $ln)) . '</li>';
            }
            $out[] = '<ul>' . $items . '</ul>';
            continue;
        }

        // Regular paragraph — keep intra-paragraph line breaks as <br>.
        $out[] = '<p>' . nl2br(e($para), false) . '</p>';
    }
    return implode("\n", $out);
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

function opposite_gender(?string $gender): ?string {
    return match ($gender) {
        'male' => 'female',
        'female' => 'male',
        default => null,
    };
}

function active_member_profile(int $userId): ?array {
    // profession is read by views/messages/show.php in the chat header — keep it here
    // so the meta line ("Software Engineer · Pune") renders instead of a lonely divider.
    return DB::one(
        "SELECT u.id, u.name, u.email, u.phone, u.role, u.status,
                p.gender, p.dob, p.city, p.state, p.country, p.profession, p.profile_complete
           FROM users u
           JOIN profiles p ON p.user_id = u.id
          WHERE u.id = ? AND u.role = 'member' AND u.status = 'active'",
        [$userId]
    );
}

function interest_between(int $viewerId, int $otherId): ?array {
    return DB::one(
        "SELECT *
           FROM interests
          WHERE (sender_id = ? AND receiver_id = ?)
             OR (sender_id = ? AND receiver_id = ?)
          ORDER BY FIELD(status, 'accepted', 'sent', 'declined', 'cancelled'),
                   (receiver_id = ?) DESC,
                   updated_at DESC,
                   id DESC
          LIMIT 1",
        [$viewerId, $otherId, $otherId, $viewerId, $viewerId]
    );
}

function accepted_interest_between(int $userId, int $otherId): ?array {
    return DB::one(
        "SELECT *
           FROM interests
          WHERE status = 'accepted'
            AND ((sender_id = ? AND receiver_id = ?)
              OR (sender_id = ? AND receiver_id = ?))
          ORDER BY updated_at DESC, id DESC
          LIMIT 1",
        [$userId, $otherId, $otherId, $userId]
    );
}

function can_message_member(int $userId, int $otherId): bool {
    return $userId !== $otherId && (bool) accepted_interest_between($userId, $otherId);
}

// All accepted-connection threads for a user, newest activity first, with last
// message preview and unread count from the other side. Shared by the inbox
// page and the in-chat sidebar so both stay consistent.
function message_threads(int $userId): array {
    return DB::all("SELECT
                      CASE WHEN i.sender_id = :u_case THEN i.receiver_id ELSE i.sender_id END AS other_id,
                      u.name AS other_name,
                      p.city AS other_city,
                      p.profession AS other_profession,
                      (SELECT body FROM messages mx
                        WHERE (mx.sender_id = :u_body_sender AND mx.receiver_id = u.id)
                           OR (mx.sender_id = u.id AND mx.receiver_id = :u_body_receiver)
                        ORDER BY mx.created_at DESC, mx.id DESC LIMIT 1) AS last_msg,
                      (SELECT created_at FROM messages mx
                        WHERE (mx.sender_id = :u_time_sender AND mx.receiver_id = u.id)
                           OR (mx.sender_id = u.id AND mx.receiver_id = :u_time_receiver)
                        ORDER BY mx.created_at DESC, mx.id DESC LIMIT 1) AS last_at,
                      (SELECT COUNT(*) FROM messages mu
                        WHERE mu.sender_id = u.id
                          AND mu.receiver_id = :u_unread
                          AND mu.read_at IS NULL) AS unread,
                      i.updated_at AS connected_at
                    FROM interests i
                    JOIN users u ON u.id = (CASE WHEN i.sender_id = :u_join THEN i.receiver_id ELSE i.sender_id END)
                    JOIN profiles p ON p.user_id = u.id
                    WHERE i.status = 'accepted'
                      AND (i.sender_id = :u_sender OR i.receiver_id = :u_receiver)
                      AND u.role = 'member' AND u.status = 'active'
                    ORDER BY COALESCE(last_at, i.updated_at) DESC", [
        'u_case' => $userId,
        'u_body_sender' => $userId,
        'u_body_receiver' => $userId,
        'u_time_sender' => $userId,
        'u_time_receiver' => $userId,
        'u_unread' => $userId,
        'u_join' => $userId,
        'u_sender' => $userId,
        'u_receiver' => $userId,
    ]);
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
