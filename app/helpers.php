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

const PROFILE_PHOTO_MIN = 2;
const PROFILE_PHOTO_MAX = 6;

// Fields the user marks with an asterisk on the edit form. Order matters —
// this is also the order the "please complete" banner lists them in.
function profile_required_fields(): array {
    return [
        'name'     => 'Full name',
        'gender'   => 'Gender',
        'dob'      => 'Date of birth',
        'city'     => 'City',
        'about_me' => 'About you',
        'photos'   => 'At least ' . PROFILE_PHOTO_MIN . ' profile photos',
    ];
}

// Returns [field_key => friendly_label] for every required piece still missing.
// Empty array means the profile is complete.
function profile_missing_fields(int $userId): array {
    $row = DB::one(
        "SELECT u.name, p.gender, p.dob, p.city, p.about_me,
                (SELECT COUNT(*) FROM photos WHERE user_id = u.id) AS photo_count
           FROM users u
           LEFT JOIN profiles p ON p.user_id = u.id
          WHERE u.id = ?",
        [$userId]
    );
    if (!$row) return profile_required_fields();

    $missing = [];
    foreach (profile_required_fields() as $key => $label) {
        if ($key === 'photos') {
            if ((int)($row['photo_count'] ?? 0) < PROFILE_PHOTO_MIN) $missing[$key] = $label;
        } elseif (empty($row[$key])) {
            $missing[$key] = $label;
        }
    }
    return $missing;
}

// Recompute and persist the profile_complete flag after any edit that could
// change completeness (bio save, photo upload, photo delete).
function recompute_profile_complete(int $userId): int {
    $complete = empty(profile_missing_fields($userId)) ? 1 : 0;
    DB::q('UPDATE profiles SET profile_complete = ? WHERE user_id = ?', [$complete, $userId]);
    return $complete;
}

// Friendly one-line success message after any profile-related save. If the
// profile is now complete we celebrate; otherwise we call out exactly what
// still needs doing so the user isn't left guessing.
function profile_save_flash(int $userId, string $verb = 'Profile saved'): string {
    $missing = profile_missing_fields($userId);
    if (!$missing) return $verb . '. Your profile is now complete — you can express interest and message other seekers.';
    return $verb . '. Still needed to unlock Express Interest: ' . implode(', ', array_values($missing)) . '.';
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

// =====================================================================
// Premium Membership helpers
//
// The single source of truth for "what plan does this user have and what
// are they allowed to do right now?" — used by both feature gates in
// routes and by the dashboard membership card.
// =====================================================================

// Canonical shape of the Free plan. Returned when a user has no active
// paid subscription so callers can always ->['contacts_limit'] etc.
function free_plan(): array {
    static $cached = null;
    if ($cached === null) {
        $cached = DB::one("SELECT * FROM packages WHERE slug = 'free' LIMIT 1") ?: [
            // Hard-coded fallback if the seed never ran, so nothing crashes.
            'id' => 0, 'slug' => 'free', 'name' => 'Free',
            'price' => 0, 'currency' => 'INR', 'duration_days' => 0,
            'duration_months' => 0, 'priority_rank' => 1,
            'contacts_limit' => 0, 'interests_per_month' => 10,
            'shortlist_limit' => 20, 'boosts_per_month' => 0,
            'featured_days' => 0, 'always_featured' => 0,
            'advanced_search' => 0, 'see_who_viewed' => 0,
            'see_who_shortlisted' => 0, 'unlimited_photos' => 0,
            'unlimited_search' => 0, 'premium_badge' => 0,
            'match_suggestions' => 'Basic', 'support_tier' => 'Email',
        ];
    }
    return $cached;
}

// The active subscription row for a user, or null. We opportunistically
// expire subscriptions here too, so nobody keeps benefits past ends_at
// even if the request-boot migration missed them.
function current_subscription(int $userId): ?array {
    $sub = DB::one(
        "SELECT * FROM subscriptions
          WHERE user_id = ? AND status = 'active'
          ORDER BY ends_at DESC LIMIT 1",
        [$userId]
    );
    if ($sub && strtotime($sub['ends_at']) < time()) {
        DB::update('subscriptions', ['status' => 'expired'], ['id' => $sub['id']]);
        return null;
    }
    return $sub ?: null;
}

// The full plan row for a user (Free if no active sub).
function current_plan(int $userId): array {
    $sub = current_subscription($userId);
    if (!$sub) return free_plan();
    $pkg = DB::one('SELECT * FROM packages WHERE id = ?', [$sub['package_id']]);
    return $pkg ?: free_plan();
}

// Feature check. Accepts short capability keys used across the app.
// Returns true if the plan allows the capability.
function plan_can(array $plan, string $capability): bool {
    return match ($capability) {
        'advanced_search'    => (bool)($plan['advanced_search'] ?? 0),
        'see_who_viewed'     => (bool)($plan['see_who_viewed'] ?? 0),
        'see_who_shortlisted'=> (bool)($plan['see_who_shortlisted'] ?? 0),
        'unlimited_shortlist'=> (int)($plan['shortlist_limit'] ?? 20) === 0,
        'unlimited_interests'=> (int)($plan['interests_per_month'] ?? 10) === 0,
        'unlimited_contacts' => (int)($plan['contacts_limit'] ?? 0) === 0
                                && ((int)($plan['price'] ?? 0) > 0),
        'view_contacts'      => (int)($plan['contacts_limit'] ?? 0) !== 0
                                || (int)($plan['price'] ?? 0) > 0,
        'premium_badge'      => (bool)($plan['premium_badge'] ?? 0),
        default              => false,
    };
}

function plan_priority_label(array $plan): string {
    $labels = [1 => 'Lowest', 2 => 'Standard', 3 => 'Medium', 4 => 'High', 5 => 'Highest'];
    $rank = max(1, min(5, (int)($plan['priority_rank'] ?? 1)));
    return $labels[$rank];
}

function profile_priority_join_sql(): string {
    return "LEFT JOIN subscriptions sub ON sub.user_id = u.id AND sub.status = 'active' AND sub.ends_at >= NOW()
            LEFT JOIN packages planpkg ON planpkg.id = sub.package_id";
}

function profile_priority_select_sql(): string {
    return "COALESCE(planpkg.priority_rank, 1) AS plan_priority_rank,
            COALESCE(planpkg.name, 'Free') AS plan_name,
            COALESCE(planpkg.premium_badge, 0) AS premium_badge,
            COALESCE(planpkg.always_featured, 0) AS always_featured,
            CASE WHEN p.boost_until IS NOT NULL AND p.boost_until > NOW() THEN 1 ELSE 0 END AS is_boosted,
            CASE WHEN COALESCE(planpkg.always_featured, 0) = 1
                   OR (p.featured_until IS NOT NULL AND p.featured_until > NOW())
                 THEN 1 ELSE 0 END AS is_featured";
}

function profile_priority_order_sql(string $fallback = 'u.created_at DESC'): string {
    return "is_boosted DESC, is_featured DESC, plan_priority_rank DESC, p.profile_complete DESC, {$fallback}";
}

// How many interests the user has already sent this calendar month.
function interests_used_this_month(int $userId): int {
    $month = date('Y-m');
    $counter = (int) DB::val(
        'SELECT count FROM interest_counters WHERE user_id = ? AND year_month = ?',
        [$userId, $month]
    );
    $legacy = (int) DB::val(
        "SELECT COUNT(*) FROM interests
          WHERE sender_id = ? AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')",
        [$userId]
    );
    return max($counter, $legacy);
}

// Remaining interests for the month — null means unlimited.
function interests_left(int $userId, ?array $plan = null): ?int {
    $plan = $plan ?? current_plan($userId);
    $cap = (int)($plan['interests_per_month'] ?? 10);
    if ($cap <= 0) return null; // unlimited
    return max(0, $cap - interests_used_this_month($userId));
}

function consume_interest_quota(int $userId, ?array $plan = null): bool {
    $plan = $plan ?? current_plan($userId);
    if (plan_can($plan, 'unlimited_interests')) return true;
    $left = interests_left($userId, $plan);
    if ($left !== null && $left <= 0) return false;
    $month = date('Y-m');
    DB::q(
        "INSERT INTO interest_counters (user_id, year_month, count)
         VALUES (?, ?, 1)
         ON DUPLICATE KEY UPDATE count = count + 1",
        [$userId, $month]
    );
    return true;
}

function shortlists_used(int $userId): int {
    return (int) DB::val('SELECT COUNT(*) FROM shortlists WHERE user_id = ?', [$userId]);
}

function shortlists_left(int $userId, ?array $plan = null): ?int {
    $plan = $plan ?? current_plan($userId);
    $cap = (int)($plan['shortlist_limit'] ?? 20);
    if ($cap <= 0) return null;
    return max(0, $cap - shortlists_used($userId));
}

// Distinct people whose contact this user has already unlocked under
// their current subscription (or lifetime, for Free / one-off packs).
function contacts_viewed_count(int $userId, ?array $sub = null): int {
    $sub = $sub ?? current_subscription($userId);
    if ($sub) {
        return (int) DB::val(
            "SELECT COUNT(DISTINCT viewed_user_id) FROM contact_views
              WHERE viewer_user_id = ? AND subscription_id = ?",
            [$userId, $sub['id']]
        );
    }
    return (int) DB::val(
        "SELECT COUNT(DISTINCT viewed_user_id) FROM contact_views
          WHERE viewer_user_id = ? AND subscription_id IS NULL",
        [$userId]
    );
}

// Remaining contact unlocks. null = unlimited.
function contacts_left(int $userId, ?array $plan = null, ?array $sub = null): ?int {
    $plan = $plan ?? current_plan($userId);
    $sub  = $sub  ?? current_subscription($userId);
    $cap = (int)($plan['contacts_limit'] ?? 0);
    // Free plan cannot view contacts at all.
    if ($cap === 0 && (int)($plan['price'] ?? 0) === 0) return 0;
    // Extra credits from the Contact Pack add-on stack on top.
    $extra = (int) DB::val('SELECT extra_contact_credits FROM profiles WHERE user_id = ?', [$userId]);
    if ($cap === 0) return null; // unlimited paid plan
    return max(0, $cap + $extra - contacts_viewed_count($userId, $sub));
}

function contact_unlocked(int $viewerId, int $viewedId): bool {
    $sub = current_subscription($viewerId);
    return (bool) DB::val(
        'SELECT 1 FROM contact_views WHERE viewer_user_id = ? AND viewed_user_id = ? AND ' .
        ($sub ? 'subscription_id = ?' : 'subscription_id IS NULL'),
        $sub ? [$viewerId, $viewedId, $sub['id']] : [$viewerId, $viewedId]
    );
}

function profile_photo_limit(int $userId): ?int {
    $plan = current_plan($userId);
    return (int)($plan['unlimited_photos'] ?? 0) === 1 ? null : PROFILE_PHOTO_MAX;
}

// Boosts used this month by the plan-quota (add-on boosts don't count against the plan).
function boosts_used_this_month(int $userId): int {
    return (int) DB::val(
        "SELECT COUNT(*) FROM profile_boosts
          WHERE user_id = ? AND source = 'plan'
            AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')",
        [$userId]
    );
}

// Remaining plan-granted boosts this month.
function boosts_left(int $userId, ?array $plan = null): int {
    $plan = $plan ?? current_plan($userId);
    $per = (int)($plan['boosts_per_month'] ?? 0);
    if ($per <= 0) return 0;
    return max(0, $per - boosts_used_this_month($userId));
}

// Compact bundle for the dashboard card / API responses. Everything the
// membership widget renders comes from here, so the widget stays dumb.
function membership_summary(int $userId): array {
    $sub  = current_subscription($userId);
    $plan = $sub
        ? (DB::one('SELECT * FROM packages WHERE id = ?', [$sub['package_id']]) ?: free_plan())
        : free_plan();
    $daysLeft = null;
    if ($sub) {
        $secs = strtotime($sub['ends_at']) - time();
        $daysLeft = max(0, (int) ceil($secs / 86400));
    }
    $prof = DB::one('SELECT featured_until, boost_until, verified_tier FROM profiles WHERE user_id = ?', [$userId]);
    return [
        'plan'            => $plan,
        'subscription'    => $sub,
        'is_free'         => !$sub,
        'expires_at'      => $sub['ends_at'] ?? null,
        'days_left'       => $daysLeft,
        'interests_left'  => interests_left($userId, $plan),
        'contacts_left'   => contacts_left($userId, $plan, $sub),
        'shortlists_left' => shortlists_left($userId, $plan),
        'boosts_left'     => boosts_left($userId, $plan),
        'featured_until'  => $prof['featured_until'] ?? null,
        'boost_until'     => $prof['boost_until']    ?? null,
        'verified_tier'   => $prof['verified_tier']  ?? 'none',
        'priority_label'  => plan_priority_label($plan),
        'badge'           => (int)($plan['premium_badge'] ?? 0) ? $plan['name'] : null,
    ];
}

// Is this user's profile currently featured? (Always for Eternal, timed
// window for Divine / Soul Elite / Featured add-on.)
function is_featured(int $userId): bool {
    $plan = current_plan($userId);
    if ((int)($plan['always_featured'] ?? 0) === 1) return true;
    $until = DB::val('SELECT featured_until FROM profiles WHERE user_id = ?', [$userId]);
    return $until && strtotime($until) > time();
}

// Is this user's profile boosted right now?
function is_boosted(int $userId): bool {
    $until = DB::val('SELECT boost_until FROM profiles WHERE user_id = ?', [$userId]);
    return $until && strtotime($until) > time();
}

// Human-readable membership badge shown next to the user's name.
function membership_badge(int $userId): ?string {
    $plan = current_plan($userId);
    if ((int)($plan['premium_badge'] ?? 0) === 0) return null;
    return $plan['name'];
}

// Activate a paid membership after a successful payment. Kept as one
// function so the Razorpay verify + webhook paths agree on the record shape.
function activate_membership(int $userId, array $pkg, string $paymentRef, float $amount, ?int $paymentRowId = null): int {
    // A new subscription always supersedes any existing active one — otherwise upgrades
    // would leave two active rows and current_subscription() would pick the older ends_at.
    DB::q("UPDATE subscriptions SET status = 'cancelled', cancelled_at = NOW()
            WHERE user_id = ? AND status = 'active'", [$userId]);
    $days = max(1, (int)$pkg['duration_days']);
    $subId = DB::insert('subscriptions', [
        'user_id'      => $userId,
        'package_id'   => $pkg['id'],
        'starts_at'    => date('Y-m-d H:i:s'),
        'purchased_at' => date('Y-m-d H:i:s'),
        'ends_at'      => date('Y-m-d H:i:s', strtotime("+{$days} days")),
        'status'       => 'active',
        'payment_ref'  => $paymentRef,
        'payment_id'   => $paymentRef,
        'amount'       => $amount,
    ]);
    // Kick off any plan-included featured window.
    $featDays = (int)($pkg['featured_days'] ?? 0);
    if ($featDays > 0) {
        DB::q("UPDATE profiles SET featured_until = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE user_id = ?", [$featDays, $userId]);
    }
    if ((int)($pkg['always_featured'] ?? 0) === 1) {
        DB::q('UPDATE profiles SET featured_until = NULL WHERE user_id = ?', [$userId]);
    }
    return (int) $subId;
}

function grant_manual_membership(int $userId, int $packageId, int $adminId, ?int $days = null): int {
    $pkg = DB::one('SELECT * FROM packages WHERE id = ?', [$packageId]);
    if (!$pkg || (float)$pkg['price'] <= 0) {
        throw new RuntimeException('Choose a paid membership plan.');
    }
    if ($days !== null && $days > 0) {
        $pkg['duration_days'] = $days;
    }
    $subId = activate_membership($userId, $pkg, 'manual-admin-' . $adminId . '-' . time(), 0.0, null);
    DB::update('subscriptions', ['granted_by_admin_id' => $adminId], ['id' => $subId]);
    return $subId;
}

function extend_membership(int $subscriptionId, int $days): bool {
    $days = max(1, $days);
    $sub = DB::one('SELECT * FROM subscriptions WHERE id = ?', [$subscriptionId]);
    if (!$sub) return false;
    DB::q(
        "UPDATE subscriptions
            SET ends_at = DATE_ADD(GREATEST(ends_at, NOW()), INTERVAL ? DAY),
                status = 'active',
                cancelled_at = NULL
          WHERE id = ?",
        [$days, $subscriptionId]
    );
    return true;
}

function cancel_membership(int $subscriptionId): bool {
    return DB::update('subscriptions', [
        'status' => 'cancelled',
        'cancelled_at' => date('Y-m-d H:i:s'),
    ], ['id' => $subscriptionId]) > 0;
}

// Grant an add-on benefit after purchase (boost window, featured window, contacts, etc).
function activate_addon(int $userId, array $addon, ?int $paymentRowId = null): int {
    $starts = date('Y-m-d H:i:s');
    $ends   = null;
    if ((int)$addon['duration_days'] > 0) {
        $ends = date('Y-m-d H:i:s', strtotime("+{$addon['duration_days']} days"));
    }
    $id = DB::insert('addon_purchases', [
        'user_id'    => $userId,
        'addon_id'   => $addon['id'],
        'payment_id' => $paymentRowId,
        'starts_at'  => $starts,
        'ends_at'    => $ends,
        'status'     => 'active',
        'amount'     => $addon['price'],
    ]);
    switch ($addon['kind']) {
        case 'boost':
            DB::insert('profile_boosts', [
                'user_id'   => $userId, 'source' => 'addon',
                'starts_at' => $starts, 'ends_at' => $ends ?: $starts,
            ]);
            DB::q("UPDATE profiles SET boost_until = GREATEST(COALESCE(boost_until, NOW()), ?) WHERE user_id = ?", [$ends, $userId]);
            break;
        case 'spotlight':
            DB::q("UPDATE profiles SET spotlight_until = GREATEST(COALESCE(spotlight_until, NOW()), ?) WHERE user_id = ?", [$ends, $userId]);
            break;
        case 'featured':
            DB::q("UPDATE profiles SET featured_until = GREATEST(COALESCE(featured_until, NOW()), ?) WHERE user_id = ?", [$ends, $userId]);
            break;
        case 'contact_pack':
            $qty = max(0, (int)$addon['quantity']);
            DB::q("UPDATE profiles SET extra_contact_credits = extra_contact_credits + ? WHERE user_id = ?", [$qty, $userId]);
            break;
        case 'review':
            // Fulfilment happens off-platform; we just note it here.
            break;
    }
    return (int) $id;
}

// Use one plan boost — increments boost_until by ~7 days. Returns false if quota exhausted.
function consume_plan_boost(int $userId): bool {
    if (boosts_left($userId) <= 0) return false;
    $ends = date('Y-m-d H:i:s', strtotime('+7 days'));
    DB::insert('profile_boosts', [
        'user_id' => $userId, 'source' => 'plan',
        'starts_at' => date('Y-m-d H:i:s'), 'ends_at' => $ends,
    ]);
    DB::q("UPDATE profiles SET boost_until = GREATEST(COALESCE(boost_until, NOW()), ?) WHERE user_id = ?", [$ends, $userId]);
    return true;
}

function primary_photo(int $userId): ?string {
    $p = DB::val('SELECT path FROM photos WHERE user_id = ? ORDER BY is_primary DESC, id ASC LIMIT 1', [$userId]);
    return $p ?: null;
}

// ---------------------------------------------------------------------------
// Verification media (govt IDs, live selfies). These files carry PII, so they
// live in storage/verification (outside the web root), are validated by real
// MIME sniffing — not the client-supplied extension — and are only readable
// through the authenticated streaming routes below.
// ---------------------------------------------------------------------------

// Validate + move an uploaded verification file. $allowed maps sniffed MIME
// type => stored extension. Returns the path relative to verify_dir, or null.
function store_verification_upload(array $file, string $dir, string $prefix, array $allowed, int $maxBytes): ?string {
    if ($file['size'] > $maxBytes || $file['size'] <= 0) return null;
    $mime = mime_content_type($file['tmp_name']) ?: '';
    if (!isset($allowed[$mime])) return null;
    if (!is_dir($dir)) mkdir($dir, 0770, true);
    $name = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) return null;
    return basename($dir) . '/' . $name;
}

// Stream a verification document (already authorised by the caller — pass the
// request row only after checking ownership or admin role).
function stream_verification_media(?array $req, string $kind): void {
    $col = $kind === 'selfie' ? 'selfie_path' : 'id_doc_path';
    $rel = $req[$col] ?? null;
    $base = realpath($GLOBALS['CFG']['uploads']['verify_dir']);
    $full = $rel && $base ? realpath($base . '/' . $rel) : false;
    if (!$full || !str_starts_with($full, $base)) {
        http_response_code(404);
        view('errors/404');
        return;
    }
    $mime = mime_content_type($full) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($full));
    header('Cache-Control: private, no-store');
    header('X-Content-Type-Options: nosniff');
    readfile($full);
    exit;
}

// Delete a request's stored documents. Called after an approve/reject decision
// (data-minimisation: we don't retain govt IDs longer than the review needs)
// and when a request is discarded.
function purge_verification_documents(array $req): void {
    $base = $GLOBALS['CFG']['uploads']['verify_dir'];
    foreach (['id_doc_path', 'selfie_path'] as $col) {
        if (!empty($req[$col])) @unlink($base . '/' . $req[$col]);
    }
    DB::update('verification_requests', ['id_doc_path' => null, 'selfie_path' => null], ['id' => $req['id']]);
}

// Small reusable checkmark badge. $tier: 'identity' | 'selfie'.
function verified_badge(?string $tier, string $extra = ''): string {
    if (!$tier || $tier === 'none') return '';
    $label = $tier === 'selfie' ? 'ID + Selfie Verified' : 'ID Verified';
    return '<span class="verified-badge' . ($extra ? ' ' . e($extra) : '') . '" title="' . $label . ' — reviewed by our team">'
         . '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1.5l2.6 2 3.2-.3 1.2 3 3 1.2-.3 3.2 2 2.6-2 2.6.3 3.2-3 1.2-1.2 3-3.2-.3-2.6 2-2.6-2-3.2.3-1.2-3-3-1.2.3-3.2-2-2.6 2-2.6-.3-3.2 3-1.2 1.2-3 3.2.3z"/><path class="tick" d="M7.8 12.4l2.7 2.7 5.7-5.9" fill="none"/></svg>'
         . '<span>' . $label . '</span></span>';
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
