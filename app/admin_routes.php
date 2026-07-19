<?php
// Admin-only routes — all guarded by Auth::requireAdmin() inside each handler.
/** @var Router $r */

// Helper: require admin (used by every handler below)
$admin = function (callable $fn) {
    return function ($args = []) use ($fn) {
        Auth::require();
        Auth::requireAdmin();
        return $fn($args);
    };
};

// Dashboard
$r->get('/admin', $admin(function () {
    $stats = [
        'members'  => DB::val("SELECT COUNT(*) FROM users WHERE role='member'"),
        'active'   => DB::val("SELECT COUNT(*) FROM users WHERE role='member' AND status='active'"),
        'blocked'  => DB::val("SELECT COUNT(*) FROM users WHERE status='blocked'"),
        'posts'    => DB::val('SELECT COUNT(*) FROM blog_posts'),
        'stories'  => DB::val('SELECT COUNT(*) FROM happy_stories'),
        'packages' => DB::val('SELECT COUNT(*) FROM packages'),
        'messages' => DB::val('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0'),
        'interests'=> DB::val('SELECT COUNT(*) FROM interests'),
        'subscribers' => DB::val("SELECT COUNT(*) FROM subscriptions WHERE status = 'active' AND ends_at >= NOW()"),
        'revenue' => DB::val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid' AND purchase_type = 'package'"),
    ];
    $recent = DB::all("SELECT u.id, u.name, u.email, u.created_at, p.gender, p.city
                       FROM users u LEFT JOIN profiles p ON p.user_id = u.id
                       WHERE u.role='member' ORDER BY u.created_at DESC LIMIT 8");
    view('admin/dashboard', compact('stats','recent'), 'admin');
}));

// ---------- USERS ----------
$r->get('/admin/users', $admin(function () {
    $q = trim($_GET['q'] ?? '');
    $where = ["u.role = 'member'"];
    $params = [];
    if ($q) {
        $where[] = "(u.name LIKE :q OR u.email LIKE :q)";
        $params['q'] = "%$q%";
    }
    $rows = DB::all("SELECT u.*, p.gender, p.dob, p.city FROM users u LEFT JOIN profiles p ON p.user_id = u.id
                     WHERE " . implode(' AND ', $where) . " ORDER BY u.created_at DESC LIMIT 200", $params);
    view('admin/users_index', ['rows' => $rows, 'q' => $q], 'admin');
}));

$r->get('/admin/users/{id}', $admin(function ($a) {
    $u  = DB::one('SELECT * FROM users WHERE id = ?', [$a['id']]);
    if (!$u) { http_response_code(404); view('errors/404'); return; }
    $p  = DB::one('SELECT * FROM profiles WHERE user_id = ?', [$a['id']]);
    $sp = DB::one('SELECT * FROM spiritual_details WHERE user_id = ?', [$a['id']]);
    $membership = $u['role'] === 'member' ? membership_summary((int)$u['id']) : null;
    $verification = DB::one("SELECT * FROM verification_requests WHERE user_id = ? ORDER BY id DESC LIMIT 1", [$a['id']]);
    view('admin/user_show', compact('u','p','sp','membership','verification'), 'admin');
}));

$r->post('/admin/users/{id}/toggle', $admin(function ($a) {
    $u = DB::one('SELECT * FROM users WHERE id = ?', [$a['id']]);
    if ($u) {
        $new = $u['status'] === 'blocked' ? 'active' : 'blocked';
        DB::update('users', ['status' => $new], ['id' => $a['id']]);
        flash('success', "User marked $new.");
    }
    redirect('/admin/users');
}));

$r->post('/admin/users/{id}/delete', $admin(function ($a) {
    DB::q('DELETE FROM users WHERE id = ? AND role = "member"', [$a['id']]);
    flash('success', 'User deleted.');
    redirect('/admin/users');
}));

// Generate a single-use password reset link admin can share with the member
// (useful when email delivery fails — common on cheap shared hosting).
$r->post('/admin/users/{id}/reset-link', $admin(function ($a) {
    $u = DB::one('SELECT id, email FROM users WHERE id = ?', [$a['id']]);
    if (!$u) { http_response_code(404); view('errors/404'); return; }
    $token = bin2hex(random_bytes(32));
    DB::insert('password_resets', [
        'user_id'      => $u['id'],
        'token_hash'   => hash('sha256', $token),
        'expires_at'   => date('Y-m-d H:i:s', strtotime('+60 minutes')),
        'requested_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
    $url = rtrim($GLOBALS['CFG']['app']['url'] ?? '', '/') . '/reset-password/' . $token;
    // Stash the link in the flash so it renders once on the user page.
    flash('success', 'Reset link generated (valid 60 min, single-use). Copy and send to the member:|' . $url);
    redirect('/admin/users/' . (int)$a['id']);
}));

$r->post('/admin/test-mail', $admin(function () {
    $u = Auth::user();
    $to = $u['email'] ?? '';
    if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Your admin account does not have a valid email address.');
        redirect('/admin');
    }

    $siteName = setting('site_name', 'Spiritual Matrimony');
    $sent = send_transactional_mail(
        $to,
        "Test email from {$siteName}",
        "This is a test email from {$siteName}.\n\nIf you received this, SMTP email delivery is working.\n",
        setting('contact_email', $GLOBALS['CFG']['mail']['from'] ?? null)
    );
    $error = $GLOBALS['last_mail_error'] ?? 'Unknown mail error.';
    flash($sent ? 'success' : 'error', $sent
        ? 'Test email sent to your admin email.'
        : 'Test email failed: ' . $error);
    redirect('/admin');
}));

// ---------- BLOG ----------
$r->get('/admin/blog', $admin(function () {
    $rows = DB::all('SELECT * FROM blog_posts ORDER BY id DESC');
    view('admin/blog_index', ['rows' => $rows], 'admin');
}));

$r->get('/admin/blog/new', $admin(function () {
    view('admin/blog_form', ['post' => null], 'admin');
}));

$r->post('/admin/blog', $admin(function () {
    $data = admin_blog_data();
    if (!$data['slug']) $data['slug'] = slugify($data['title']);
    $data['published_at'] = $data['published'] ? date('Y-m-d H:i:s') : null;
    DB::insert('blog_posts', $data);
    flash('success','Post published.');
    redirect('/admin/blog');
}));

$r->get('/admin/blog/{id}/edit', $admin(function ($a) {
    $post = DB::one('SELECT * FROM blog_posts WHERE id = ?', [$a['id']]);
    if (!$post) { http_response_code(404); view('errors/404'); return; }
    view('admin/blog_form', ['post' => $post], 'admin');
}));

$r->post('/admin/blog/{id}', $admin(function ($a) {
    $data = admin_blog_data();
    if (!$data['slug']) $data['slug'] = slugify($data['title']);
    DB::update('blog_posts', $data, ['id' => $a['id']]);
    flash('success','Post updated.');
    redirect('/admin/blog');
}));

$r->post('/admin/blog/{id}/delete', $admin(function ($a) {
    DB::q('DELETE FROM blog_posts WHERE id = ?', [$a['id']]);
    flash('success','Post deleted.');
    redirect('/admin/blog');
}));

function admin_blog_data(): array {
    $cover = $_POST['cover_image'] ?? null;
    if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $dir = $GLOBALS['CFG']['uploads']['blog_dir'];
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $name = bin2hex(random_bytes(6)) . '.' . $ext;
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $dir . '/' . $name)) {
            $cover = 'blog/' . $name;
        }
    }
    return [
        'title'       => trim($_POST['title'] ?? ''),
        'slug'        => slugify($_POST['slug'] ?? $_POST['title'] ?? ''),
        'excerpt'     => $_POST['excerpt'] ?? '',
        'body'        => $_POST['body'] ?? '',
        'cover_image' => $cover,
        'category'    => $_POST['category'] ?? 'General',
        'author_name' => $_POST['author_name'] ?? 'Editorial Team',
        'published'   => isset($_POST['published']) ? 1 : 0,
    ];
}

// ---------- PACKAGES ----------
$r->get('/admin/packages', $admin(function () {
    $rows = DB::all('SELECT * FROM packages ORDER BY display_order, id');
    view('admin/packages_index', ['rows' => $rows], 'admin');
}));

$r->get('/admin/packages/new', $admin(function () {
    view('admin/packages_form', ['pkg' => null], 'admin');
}));

$r->post('/admin/packages', $admin(function () {
    DB::insert('packages', admin_pkg_data());
    flash('success','Package added.');
    redirect('/admin/packages');
}));

$r->get('/admin/packages/{id}/edit', $admin(function ($a) {
    $pkg = DB::one('SELECT * FROM packages WHERE id = ?', [$a['id']]);
    if (!$pkg) { http_response_code(404); view('errors/404'); return; }
    view('admin/packages_form', ['pkg' => $pkg], 'admin');
}));

$r->post('/admin/packages/{id}', $admin(function ($a) {
    DB::update('packages', admin_pkg_data(), ['id' => $a['id']]);
    flash('success','Package updated.');
    redirect('/admin/packages');
}));

$r->post('/admin/packages/{id}/delete', $admin(function ($a) {
    try {
        DB::q('DELETE FROM packages WHERE id = ?', [$a['id']]);
        flash('success','Package deleted.');
    } catch (Throwable $e) {
        DB::update('packages', ['is_active' => 0], ['id' => $a['id']]);
        flash('success','Package has subscribers, so it was hidden instead of deleted.');
    }
    redirect('/admin/packages');
}));

function admin_pkg_data(): array {
    $slug = trim((string)($_POST['slug'] ?? ''));
    if ($slug === '') $slug = slugify($_POST['name'] ?? '');
    $durationMonths = max(0, (int)($_POST['duration_months'] ?? 1));
    $durationDays = (int)($_POST['duration_days'] ?? 0);
    if ($durationDays <= 0) {
        $durationDays = $durationMonths > 0 ? $durationMonths * 30 : 36500;
    }
    return [
        'slug'           => $slug,
        'name'           => trim($_POST['name'] ?? ''),
        'tagline'        => $_POST['tagline'] ?? null,
        'price'          => (float)($_POST['price'] ?? 0),
        'currency'       => $_POST['currency'] ?? 'INR',
        'duration_days'  => $durationDays,
        'duration_months'=> $durationMonths,
        'monthly_display'=> ($_POST['monthly_display'] ?? '') === '' ? null : (float)$_POST['monthly_display'],
        'savings_badge'  => trim($_POST['savings_badge'] ?? '') ?: null,
        'ribbon'         => trim($_POST['ribbon'] ?? '') ?: null,
        'priority_rank'  => max(1, min(5, (int)($_POST['priority_rank'] ?? 1))),
        'contacts_limit' => (int)($_POST['contacts_limit'] ?? 0),
        'interests_per_month' => (int)($_POST['interests_per_month'] ?? 10),
        'shortlist_limit'=> (int)($_POST['shortlist_limit'] ?? 20),
        'boosts_per_month' => (int)($_POST['boosts_per_month'] ?? 0),
        'featured_days'  => (int)($_POST['featured_days'] ?? 0),
        'always_featured'=> isset($_POST['always_featured']) ? 1 : 0,
        'advanced_search'=> isset($_POST['advanced_search']) ? 1 : 0,
        'see_who_viewed' => isset($_POST['see_who_viewed']) ? 1 : 0,
        'see_who_shortlisted' => isset($_POST['see_who_shortlisted']) ? 1 : 0,
        'unlimited_photos' => isset($_POST['unlimited_photos']) ? 1 : 0,
        'unlimited_search' => isset($_POST['unlimited_search']) ? 1 : 0,
        'premium_badge'  => isset($_POST['premium_badge']) ? 1 : 0,
        'match_suggestions' => trim($_POST['match_suggestions'] ?? 'Basic') ?: 'Basic',
        'support_tier'   => trim($_POST['support_tier'] ?? 'Email') ?: 'Email',
        'features'       => $_POST['features'] ?? '',
        'highlighted'    => isset($_POST['highlighted']) ? 1 : 0,
        'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        'display_order'  => (int)($_POST['display_order'] ?? 0),
    ];
}

// ---------- SUBSCRIBERS ----------
$r->get('/admin/subscribers', $admin(function () {
    $rows = DB::all("SELECT s.*, u.name AS user_name, u.email AS user_email, pk.name AS package_name, pk.slug AS package_slug,
                            pay.gateway_payment_id, pay.gateway_order_id
                       FROM subscriptions s
                       JOIN users u ON u.id = s.user_id
                       JOIN packages pk ON pk.id = s.package_id
                  LEFT JOIN payments pay ON pay.subscription_id = s.id
                   ORDER BY FIELD(s.status, 'active','pending','expired','cancelled'), s.ends_at DESC
                      LIMIT 300");
    $packages = DB::all("SELECT * FROM packages WHERE is_active = 1 AND price > 0 ORDER BY display_order, id");
    $members = DB::all("SELECT id, name, email FROM users WHERE role = 'member' ORDER BY name LIMIT 500");
    $stats = [
        'active' => DB::val("SELECT COUNT(*) FROM subscriptions WHERE status = 'active' AND ends_at >= NOW()"),
        'expired' => DB::val("SELECT COUNT(*) FROM subscriptions WHERE status = 'expired'"),
        'revenue' => DB::val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid' AND purchase_type = 'package'"),
        'paid_payments' => DB::val("SELECT COUNT(*) FROM payments WHERE status = 'paid' AND purchase_type = 'package'"),
    ];
    view('admin/subscribers', compact('rows','packages','members','stats'), 'admin');
}));

$r->post('/admin/subscribers/grant', $admin(function () {
    $userId = (int)($_POST['user_id'] ?? 0);
    $packageId = (int)($_POST['package_id'] ?? 0);
    $days = (int)($_POST['days'] ?? 0);
    try {
        grant_manual_membership($userId, $packageId, Auth::id(), $days > 0 ? $days : null);
        flash('success','Manual membership granted.');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
    redirect('/admin/subscribers');
}));

$r->post('/admin/subscribers/{id}/cancel', $admin(function ($a) {
    cancel_membership((int)$a['id']);
    flash('success','Membership cancelled. The member will use the Free plan.');
    redirect('/admin/subscribers');
}));

$r->post('/admin/subscribers/{id}/extend', $admin(function ($a) {
    $days = max(1, (int)($_POST['days'] ?? 30));
    extend_membership((int)$a['id'], $days);
    flash('success','Membership extended by ' . $days . ' days.');
    redirect('/admin/subscribers');
}));

// ---------- REVENUE ----------
$r->get('/admin/revenue', $admin(function () {
    $totals = [
        'all'          => (float) DB::val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid'"),
        'package'      => (float) DB::val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid' AND purchase_type = 'package'"),
        'addon'        => (float) DB::val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid' AND purchase_type = 'addon'"),
        'verification' => (float) DB::val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid' AND purchase_type = 'verification'"),
        'this_month'   => (float) DB::val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid' AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"),
    ];
    // Revenue by plan.
    $byPlan = DB::all("SELECT pk.id, pk.slug, pk.name, pk.price,
                              COUNT(pay.id) AS paid_orders,
                              COALESCE(SUM(pay.amount), 0) AS revenue,
                              (SELECT COUNT(*) FROM subscriptions s
                                WHERE s.package_id = pk.id AND s.status = 'active' AND s.ends_at >= NOW()) AS active_subs
                         FROM packages pk
                    LEFT JOIN payments pay ON pay.package_id = pk.id AND pay.status = 'paid' AND pay.purchase_type = 'package'
                        WHERE pk.price > 0
                     GROUP BY pk.id, pk.slug, pk.name, pk.price
                     ORDER BY pk.display_order, pk.id");
    // Revenue by add-on.
    $byAddon = DB::all("SELECT ad.id, ad.slug, ad.name,
                               COUNT(pay.id) AS paid_orders,
                               COALESCE(SUM(pay.amount), 0) AS revenue
                          FROM addons ad
                     LEFT JOIN payments pay ON pay.item_id = ad.id AND pay.status = 'paid' AND pay.purchase_type = 'addon'
                      GROUP BY ad.id, ad.slug, ad.name
                      ORDER BY ad.display_order, ad.id");
    // Monthly rollup (last 12 months).
    $monthly = DB::all("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym,
                               COUNT(*) AS orders,
                               COALESCE(SUM(amount), 0) AS revenue
                          FROM payments
                         WHERE status = 'paid'
                           AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                      GROUP BY ym
                      ORDER BY ym DESC");
    view('admin/revenue', compact('totals','byPlan','byAddon','monthly'), 'admin');
}));

// ---------- VERIFICATION ----------
$r->get('/admin/verification', $admin(function () {
    $filter = $_GET['status'] ?? '';
    $where = '';
    $params = [];
    if (in_array($filter, ['pending_review','pending_upload','pending_payment','approved','rejected'], true)) {
        $where = 'WHERE vr.status = ?';
        $params[] = $filter;
    }
    $rows = DB::all("SELECT vr.*, u.name AS user_name, u.email AS user_email,
                            p.verified_tier AS current_verified_tier, p.verified_at,
                            pay.gateway_payment_id, pay.gateway_order_id
                       FROM verification_requests vr
                       JOIN users u ON u.id = vr.user_id
                  LEFT JOIN profiles p ON p.user_id = vr.user_id
                  LEFT JOIN payments pay ON pay.id = vr.payment_id
                     $where
                   ORDER BY FIELD(vr.status, 'pending_review','pending_upload','pending_payment','rejected','approved'),
                            vr.created_at DESC
                      LIMIT 300", $params);
    $stats = [
        'pending'  => DB::val("SELECT COUNT(*) FROM verification_requests WHERE status = 'pending_review'"),
        'awaiting' => DB::val("SELECT COUNT(*) FROM verification_requests WHERE status IN ('pending_payment','pending_upload')"),
        'approved' => DB::val("SELECT COUNT(*) FROM verification_requests WHERE status = 'approved'"),
        'rejected' => DB::val("SELECT COUNT(*) FROM verification_requests WHERE status = 'rejected'"),
        'revenue'  => DB::val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid' AND purchase_type = 'verification'"),
    ];
    $prices = [
        'identity' => setting('verify_identity_price', '299'),
        'selfie'   => setting('verify_selfie_price', '499'),
    ];
    view('admin/verification', compact('rows','stats','prices','filter'), 'admin');
}));

// Review one request: submitted ID + selfie side by side with the member's
// profile photos, so the admin can genuinely compare before deciding.
$r->get('/admin/verification/{id}', $admin(function ($a) {
    $req = DB::one("SELECT vr.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone,
                           u.created_at AS user_joined,
                           p.dob, p.city, p.state, p.gender, p.verified_tier AS current_verified_tier,
                           pay.gateway_payment_id,
                           rev.name AS reviewer_name
                      FROM verification_requests vr
                      JOIN users u ON u.id = vr.user_id
                 LEFT JOIN profiles p ON p.user_id = vr.user_id
                 LEFT JOIN payments pay ON pay.id = vr.payment_id
                 LEFT JOIN users rev ON rev.id = vr.reviewed_by
                     WHERE vr.id = ?", [$a['id']]);
    if (!$req) { http_response_code(404); view('errors/404'); return; }
    $photos = DB::all('SELECT * FROM photos WHERE user_id = ? ORDER BY is_primary DESC, id LIMIT 6', [$req['user_id']]);
    $history = DB::all('SELECT * FROM verification_requests WHERE user_id = ? AND id != ? ORDER BY id DESC', [$req['user_id'], $req['id']]);
    view('admin/verification_show', compact('req','photos','history'), 'admin');
}));

// Stream a submitted document to the reviewing admin.
$r->get('/admin/verification/{id}/media/{kind}', $admin(function ($a) {
    $req = DB::one('SELECT * FROM verification_requests WHERE id = ?', [$a['id']]);
    stream_verification_media($req, $a['kind']);
}));

$r->post('/admin/verification/pricing', $admin(function () {
    foreach ([
        'verify_identity_price' => max(0, (int)($_POST['verify_identity_price'] ?? 299)),
        'verify_selfie_price'   => max(0, (int)($_POST['verify_selfie_price'] ?? 499)),
    ] as $key => $value) {
        if (DB::val('SELECT 1 FROM site_settings WHERE setting_key = ?', [$key])) {
            DB::update('site_settings', ['setting_value' => (string)$value], ['setting_key' => $key]);
        } else {
            DB::insert('site_settings', ['setting_key' => $key, 'setting_value' => (string)$value]);
        }
    }
    flash('success','Verification pricing updated.');
    redirect('/admin/verification');
}));

$r->post('/admin/verification/{id}/approve', $admin(function ($a) {
    $req = DB::one('SELECT * FROM verification_requests WHERE id = ?', [$a['id']]);
    if (!$req) { http_response_code(404); view('errors/404'); return; }
    $notes = trim((string)($_POST['admin_notes'] ?? ''));
    DB::update('verification_requests', [
        'status'      => 'approved',
        'admin_notes' => $notes ?: null,
        'reviewed_at' => date('Y-m-d H:i:s'),
        'reviewed_by' => Auth::id(),
    ], ['id' => $req['id']]);
    DB::update('profiles', [
        'verified_tier' => $req['tier'],
        'verified_at'   => date('Y-m-d H:i:s'),
    ], ['user_id' => $req['user_id']]);

    // Decision made — the govt ID has served its purpose, don't retain it.
    purge_verification_documents($req);
    DB::insert('audit_log', [
        'actor_id' => Auth::id(), 'action' => 'verification.approve',
        'target_type' => 'verification_request', 'target_id' => $req['id'],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    $user = DB::one('SELECT name, email FROM users WHERE id = ?', [$req['user_id']]);
    if ($user) {
        $badge = $req['tier'] === 'selfie' ? 'ID + Selfie Verified' : 'ID Verified';
        send_transactional_mail($user['email'], 'Your profile is now verified 🎉',
            "Namaste {$user['name']},\n\nGreat news — your verification has been approved. The \"$badge\" badge now appears on your profile, in search results and on the homepage.\n\nYour submitted documents have been permanently deleted from our systems.\n\nWith gratitude,\n" . setting('site_name', 'Spiritual Matrimony'));
    }
    flash('success','Verification approved — member notified and documents purged.');
    redirect('/admin/verification');
}));

$r->post('/admin/verification/{id}/reject', $admin(function ($a) {
    $req = DB::one('SELECT * FROM verification_requests WHERE id = ?', [$a['id']]);
    if (!$req) { http_response_code(404); view('errors/404'); return; }
    $reason = trim((string)($_POST['reject_reason'] ?? ''));
    if ($reason === '') $reason = trim((string)($_POST['reject_reason_custom'] ?? ''));
    if ($reason === '') {
        flash('error', 'Please give the member a reason — it shows on their verification page so they can fix and resubmit.');
        redirect('/admin/verification/' . (int)$req['id']);
    }
    DB::update('verification_requests', [
        'status'        => 'rejected',
        'reject_reason' => $reason,
        'admin_notes'   => trim((string)($_POST['admin_notes'] ?? '')) ?: null,
        'reviewed_at'   => date('Y-m-d H:i:s'),
        'reviewed_by'   => Auth::id(),
    ], ['id' => $req['id']]);

    purge_verification_documents($req);
    DB::insert('audit_log', [
        'actor_id' => Auth::id(), 'action' => 'verification.reject',
        'target_type' => 'verification_request', 'target_id' => $req['id'],
        'meta' => $reason,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    $user = DB::one('SELECT name, email FROM users WHERE id = ?', [$req['user_id']]);
    if ($user) {
        send_transactional_mail($user['email'], 'Verification — action needed',
            "Namaste {$user['name']},\n\nWe couldn't approve your verification this time.\n\nReason: $reason\n\nYou can resubmit your documents from the Verification page — no additional payment is needed. Your previous documents have been deleted from our systems.\n\nWith gratitude,\n" . setting('site_name', 'Spiritual Matrimony'));
    }
    flash('success','Verification rejected — member notified with the reason.');
    redirect('/admin/verification');
}));

// ---------- HAPPY STORIES ----------
$r->get('/admin/stories', $admin(function () {
    $rows = DB::all('SELECT * FROM happy_stories ORDER BY is_featured DESC, id DESC');
    view('admin/stories_index', ['rows' => $rows], 'admin');
}));

$r->get('/admin/stories/new', $admin(function () { view('admin/stories_form', ['story' => null], 'admin'); }));

$r->post('/admin/stories', $admin(function () {
    DB::insert('happy_stories', admin_story_data());
    flash('success','Story added.');
    redirect('/admin/stories');
}));

$r->get('/admin/stories/{id}/edit', $admin(function ($a) {
    $story = DB::one('SELECT * FROM happy_stories WHERE id = ?', [$a['id']]);
    view('admin/stories_form', ['story' => $story], 'admin');
}));

$r->post('/admin/stories/{id}', $admin(function ($a) {
    DB::update('happy_stories', admin_story_data(), ['id' => $a['id']]);
    flash('success','Story updated.');
    redirect('/admin/stories');
}));

$r->post('/admin/stories/{id}/delete', $admin(function ($a) {
    DB::q('DELETE FROM happy_stories WHERE id = ?', [$a['id']]);
    flash('success','Story deleted.');
    redirect('/admin/stories');
}));

function admin_story_data(): array {
    $photo = $_POST['photo'] ?? null;
    if (!empty($_FILES['photo_file']['name']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        $dir = $GLOBALS['CFG']['uploads']['site_dir'];
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $ext = strtolower(pathinfo($_FILES['photo_file']['name'], PATHINFO_EXTENSION));
        $name = 'story_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $dir . '/' . $name)) {
            $photo = 'site/' . $name;
        }
    }
    return [
        'couple_name' => trim($_POST['couple_name'] ?? ''),
        'story'       => $_POST['story'] ?? '',
        'photo'       => $photo,
        'married_on'  => $_POST['married_on'] ?: null,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
    ];
}

// ---------- CMS PAGES ----------
$r->get('/admin/pages', $admin(function () {
    $rows = DB::all('SELECT * FROM pages ORDER BY slug');
    view('admin/pages_index', ['rows' => $rows], 'admin');
}));

$r->get('/admin/pages/new', $admin(function () {
    view('admin/pages_form', ['page' => null], 'admin');
}));

$r->post('/admin/pages', $admin(function () {
    $slug = slugify(trim($_POST['slug'] ?? '') ?: ($_POST['title'] ?? ''));
    $title = trim($_POST['title'] ?? '');
    if (!$slug || !$title) {
        flash('error', 'Title and slug are required.');
        redirect('/admin/pages/new');
    }
    if (DB::val('SELECT 1 FROM pages WHERE slug = ?', [$slug])) {
        flash('error', "A page with slug '{$slug}' already exists.");
        redirect('/admin/pages/new');
    }
    DB::insert('pages', [
        'slug'      => $slug,
        'title'     => $title,
        'body'      => $_POST['body'] ?? '',
        'published' => isset($_POST['published']) ? 1 : 0,
    ]);
    flash('success', "Page created. Live at /page/{$slug}");
    redirect('/admin/pages');
}));

$r->get('/admin/pages/{id}/edit', $admin(function ($a) {
    $page = DB::one('SELECT * FROM pages WHERE id = ?', [$a['id']]);
    if (!$page) { http_response_code(404); view('errors/404'); return; }
    view('admin/pages_form', ['page' => $page], 'admin');
}));

$r->post('/admin/pages/{id}', $admin(function ($a) {
    DB::update('pages', [
        'title'     => trim($_POST['title'] ?? ''),
        'body'      => $_POST['body'] ?? '',
        'published' => isset($_POST['published']) ? 1 : 0,
    ], ['id' => $a['id']]);
    flash('success','Page updated.');
    redirect('/admin/pages');
}));

$r->post('/admin/pages/{id}/delete', $admin(function ($a) {
    $p = DB::one('SELECT slug FROM pages WHERE id = ?', [$a['id']]);
    // Protect the pages with hard-coded short URLs
    // (/about, /privacy, /terms, /contact, /refund-policy, /cookies)
    $protected = ['about','privacy','terms','contact','refund-policy','cookie-policy'];
    if ($p && in_array($p['slug'], $protected, true)) {
        flash('error', "The '{$p['slug']}' page is built-in and cannot be deleted (you can unpublish it instead).");
    } else {
        DB::q('DELETE FROM pages WHERE id = ?', [$a['id']]);
        flash('success','Page deleted.');
    }
    redirect('/admin/pages');
}));

// ---------- SITE SETTINGS ----------
$r->get('/admin/settings', $admin(function () {
    $rows = DB::all('SELECT * FROM site_settings ORDER BY setting_key');
    view('admin/settings', ['rows' => $rows], 'admin');
}));

$r->post('/admin/settings', $admin(function () {
    foreach ($_POST['settings'] ?? [] as $k => $v) {
        $exists = DB::val('SELECT 1 FROM site_settings WHERE setting_key = ?', [$k]);
        if ($exists) {
            DB::update('site_settings', ['setting_value' => $v], ['setting_key' => $k]);
        } else {
            DB::insert('site_settings', ['setting_key' => $k, 'setting_value' => $v]);
        }
    }
    flash('success','Settings saved.');
    redirect('/admin/settings');
}));

// ---------- PAYMENT DETAILS ----------
// Dedicated page for UPI ID, bank account, QR image and contact info. Stored
// in site_settings under payment_* keys so the public /payment-details page
// and footer/setting() helper can read them with no extra schema.
$PAYMENT_KEYS = [
    'payment_payee_name',
    'payment_upi_id',
    'payment_upi_qr_url',
    'payment_bank_name',
    'payment_account_name',
    'payment_account_number',
    'payment_ifsc',
    'payment_branch',
    'payment_contact_phone',
    'payment_contact_email',
    'payment_instructions',
];

$r->get('/admin/payment-details', $admin(function () use ($PAYMENT_KEYS) {
    $values = [];
    foreach ($PAYMENT_KEYS as $k) {
        $values[$k] = (string) DB::val('SELECT setting_value FROM site_settings WHERE setting_key = ?', [$k]);
    }
    view('admin/payment_details', ['values' => $values], 'admin');
}));

$r->post('/admin/payment-details', $admin(function () use ($PAYMENT_KEYS) {
    foreach ($PAYMENT_KEYS as $k) {
        $v = trim((string)($_POST[$k] ?? ''));
        $exists = DB::val('SELECT 1 FROM site_settings WHERE setting_key = ?', [$k]);
        if ($exists) {
            DB::update('site_settings', ['setting_value' => $v], ['setting_key' => $k]);
        } else {
            DB::insert('site_settings', ['setting_key' => $k, 'setting_value' => $v]);
        }
    }
    flash('success','Payment details saved. Members will see them on /payment-details.');
    redirect('/admin/payment-details');
}));

// ---------- RAZORPAY ----------
$RAZORPAY_KEYS = ['razorpay_enabled','razorpay_mode','razorpay_key_id','razorpay_key_secret','razorpay_webhook_secret'];

$r->get('/admin/razorpay', $admin(function () use ($RAZORPAY_KEYS) {
    $values = [];
    foreach ($RAZORPAY_KEYS as $k) {
        $values[$k] = (string) DB::val('SELECT setting_value FROM site_settings WHERE setting_key = ?', [$k]);
    }
    $recent = DB::all("SELECT p.*, u.name AS user_name, u.email AS user_email,
                              COALESCE(
                                  pk.name,
                                  ad.name,
                                  CASE
                                      WHEN vr.tier = 'selfie' THEN 'Selfie + Identity Verification'
                                      WHEN vr.tier = 'identity' THEN 'Identity Verification'
                                      ELSE NULL
                                  END
                              ) AS package_name
                       FROM payments p
                       LEFT JOIN users u ON u.id = p.user_id
                       LEFT JOIN packages pk ON pk.id = p.package_id
                       LEFT JOIN addons ad ON ad.id = p.item_id AND p.purchase_type = 'addon'
                       LEFT JOIN verification_requests vr ON vr.id = p.item_id AND p.purchase_type = 'verification'
                       ORDER BY p.created_at DESC LIMIT 25");
    view('admin/razorpay', ['values' => $values, 'recent' => $recent], 'admin');
}));

$r->post('/admin/razorpay', $admin(function () use ($RAZORPAY_KEYS) {
    foreach ($RAZORPAY_KEYS as $k) {
        $v = trim((string)($_POST[$k] ?? ''));
        if ($k === 'razorpay_enabled') $v = isset($_POST[$k]) ? '1' : '0';
        if ($k === 'razorpay_mode')    $v = in_array($v, ['test','live'], true) ? $v : 'test';
        $exists = DB::val('SELECT 1 FROM site_settings WHERE setting_key = ?', [$k]);
        if ($exists) {
            DB::update('site_settings', ['setting_value' => $v], ['setting_key' => $k]);
        } else {
            DB::insert('site_settings', ['setting_key' => $k, 'setting_value' => $v]);
        }
    }
    flash('success','Razorpay settings saved.');
    redirect('/admin/razorpay');
}));

// ---------- CONTACT MESSAGES ----------
$r->get('/admin/messages', $admin(function () {
    $rows = DB::all('SELECT * FROM contact_messages ORDER BY created_at DESC');
    DB::q('UPDATE contact_messages SET is_read = 1 WHERE is_read = 0');
    view('admin/messages_index', ['rows' => $rows], 'admin');
}));

$r->post('/admin/messages/{id}/delete', $admin(function ($a) {
    DB::q('DELETE FROM contact_messages WHERE id = ?', [$a['id']]);
    redirect('/admin/messages');
}));

// ---------- ADMIN PROFILE / CHANGE PASSWORD ----------
$r->get('/admin/profile', $admin(function () {
    view('admin/profile', ['u' => Auth::user()], 'admin');
}));

$r->post('/admin/profile', $admin(function () {
    $u = Auth::user();
    $data = ['name' => trim($_POST['name'] ?? $u['name']), 'email' => trim($_POST['email'] ?? $u['email'])];
    DB::update('users', $data, ['id' => Auth::id()]);
    if (!empty($_POST['new_password']) && password_verify($_POST['current_password'] ?? '', $u['password_hash'])) {
        DB::update('users', ['password_hash' => password_hash($_POST['new_password'], PASSWORD_BCRYPT)], ['id' => Auth::id()]);
        flash('success','Password updated.');
    } else {
        flash('success','Profile updated.');
    }
    redirect('/admin/profile');
}));
