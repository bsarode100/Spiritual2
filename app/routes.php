<?php
// Public + member routes (admin routes live in admin_routes.php).
/** @var Router $r */

// ------------------- HOME -------------------
$r->get('/', function () {
    $priorityJoin = profile_priority_join_sql();
    $prioritySelect = profile_priority_select_sql();
    $featured = DB::all("SELECT u.*, p.gender, p.dob, p.city, p.state, p.country, p.profession, p.about_me, p.education, p.height_cm, p.verified_tier, s.spiritual_path,
                                {$prioritySelect}
                          FROM users u
                          JOIN profiles p ON p.user_id = u.id
                     LEFT JOIN spiritual_details s ON s.user_id = u.id
                     {$priorityJoin}
                         WHERE u.role = 'member' AND u.status = 'active' AND p.profile_complete = 1
                      ORDER BY " . profile_priority_order_sql('u.created_at DESC') . " LIMIT 6");
    $stories  = DB::all("SELECT * FROM happy_stories ORDER BY is_featured DESC, id DESC LIMIT 3");
    $packages = DB::all("SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order, id");
    $posts    = DB::all("SELECT * FROM blog_posts WHERE published = 1 ORDER BY published_at DESC LIMIT 3");
    view('home', compact('featured','stories','packages','posts'));
});

// ------------------- CMS PAGES -------------------
$r->get('/page/{slug}', function ($a) {
    $page = DB::one('SELECT * FROM pages WHERE slug = ? AND published = 1', [$a['slug']]);
    if (!$page) { http_response_code(404); view('errors/404'); return; }
    view('page', ['page' => $page]);
});

$r->get('/about',          function () { $page = DB::one("SELECT * FROM pages WHERE slug='about'");          view('page', ['page' => $page]); });
$r->get('/privacy',        function () { $page = DB::one("SELECT * FROM pages WHERE slug='privacy'");        view('page', ['page' => $page]); });
$r->get('/terms',          function () { $page = DB::one("SELECT * FROM pages WHERE slug='terms'");          view('page', ['page' => $page]); });
// Legacy shortcuts — kept alive as 301 redirects so old bookmarks and outbound
// links still land on the canonical /page/{slug} version the admin now edits.
$r->get('/refund-policy',  function () { header('Location: /page/refund-policy', true, 301); exit; });
$r->get('/cookies',        function () { header('Location: /page/cookie-policy', true, 301); exit; });
// Handle old URL variants people may have shared or that other views mistakenly linked to.
$r->get('/page/privacy-policy',      function () { header('Location: /page/privacy',       true, 301); exit; });
$r->get('/page/terms-and-condition', function () { header('Location: /page/terms',         true, 301); exit; });
$r->get('/page/cookies',             function () { header('Location: /page/cookie-policy', true, 301); exit; });

// ------------------- PAYMENT DETAILS (public) -------------------
$r->get('/payment-details', function () {
    $details = [
        'payee_name'      => setting('payment_payee_name', ''),
        'upi_id'          => setting('payment_upi_id', ''),
        'upi_qr_url'      => setting('payment_upi_qr_url', ''),
        'bank_name'       => setting('payment_bank_name', ''),
        'account_name'    => setting('payment_account_name', ''),
        'account_number'  => setting('payment_account_number', ''),
        'ifsc'            => setting('payment_ifsc', ''),
        'branch'          => setting('payment_branch', ''),
        'contact_phone'   => setting('payment_contact_phone', ''),
        'contact_email'   => setting('payment_contact_email', ''),
        'instructions'    => setting('payment_instructions', ''),
    ];
    view('payment_details', ['details' => $details]);
});

// ------------------- CONTACT -------------------
$r->get('/contact', function () { view('contact'); });

$r->post('/contact', function () {
    $data = [
        'name'    => trim($_POST['name']    ?? ''),
        'email'   => trim($_POST['email']   ?? ''),
        'phone'   => trim($_POST['phone']   ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'message' => trim($_POST['message'] ?? ''),
    ];
    if (!$data['name'] || !filter_var($data['email'], FILTER_VALIDATE_EMAIL) || !$data['message']) {
        flash('error', 'Please fill name, valid email, and message.');
        redirect('/contact');
    }
    DB::insert('contact_messages', $data);
    flash('success', 'Thank you — we received your message and will write back soon.');
    redirect('/contact');
});

// ------------------- BLOG -------------------
$r->get('/blog', function () {
    $posts = DB::all('SELECT * FROM blog_posts WHERE published = 1 ORDER BY published_at DESC');
    view('blog/index', ['posts' => $posts]);
});

$r->get('/blog/{slug}', function ($a) {
    $post = DB::one('SELECT * FROM blog_posts WHERE slug = ? AND published = 1', [$a['slug']]);
    if (!$post) { http_response_code(404); view('errors/404'); return; }
    $more = DB::all('SELECT * FROM blog_posts WHERE published = 1 AND id != ? ORDER BY published_at DESC LIMIT 3', [$post['id']]);
    view('blog/show', ['post' => $post, 'more' => $more]);
});

// ------------------- PACKAGES -------------------
$r->get('/packages', function () {
    $packages = DB::all('SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order, id');
    $rzp_enabled = setting('razorpay_enabled') === '1'
                && setting('razorpay_key_id') !== ''
                && setting('razorpay_key_secret') !== '';
    $me = Auth::check() ? membership_summary(Auth::id()) : null;
    view('packages/index', ['packages' => $packages, 'rzp_enabled' => $rzp_enabled, 'me' => $me]);
});

// ------------------- ADD-ONS -------------------
$r->get('/addons', function () {
    $addons = DB::all('SELECT * FROM addons WHERE is_active = 1 ORDER BY display_order, id');
    $rzp_enabled = setting('razorpay_enabled') === '1'
                && setting('razorpay_key_id') !== ''
                && setting('razorpay_key_secret') !== '';
    view('addons/index', ['addons' => $addons, 'rzp_enabled' => $rzp_enabled]);
});

// ------------------- VERIFICATION -------------------
$r->get('/verification', function () {
    $identity = (int) setting('verify_identity_price', '299');
    $selfie   = (int) setting('verify_selfie_price', '499');
    $me = null; $existing = null;
    if (Auth::check()) {
        $me = membership_summary(Auth::id());
        $existing = DB::one("SELECT * FROM verification_requests WHERE user_id = ? ORDER BY id DESC LIMIT 1", [Auth::id()]);
    }
    view('verification/index', ['identity_price' => $identity, 'selfie_price' => $selfie, 'me' => $me, 'existing' => $existing]);
});

$r->post('/verification/start', function () {
    Auth::require();
    $uid = Auth::id();

    // One request in flight at a time; approved members don't need to pay again
    // unless they're upgrading identity -> selfie.
    $open = DB::one("SELECT * FROM verification_requests
                      WHERE user_id = ? AND status IN ('pending_payment','pending_upload','pending_review')
                      ORDER BY id DESC LIMIT 1", [$uid]);
    if ($open) {
        if ($open['status'] === 'pending_payment') { redirect('/checkout/verification/' . (int)$open['id']); }
        flash('error', 'You already have a verification request in progress.');
        redirect('/verification');
    }

    $tier = ($_POST['tier'] ?? '') === 'selfie' ? 'selfie' : 'identity';
    $current = DB::val('SELECT verified_tier FROM profiles WHERE user_id = ?', [$uid]) ?: 'none';
    if ($current === 'selfie' || $current === $tier) {
        flash('success', 'Your profile is already ' . $current . ' verified.');
        redirect('/verification');
    }

    $amount = $tier === 'selfie'
        ? (int) setting('verify_selfie_price', '499')
        : (int) setting('verify_identity_price', '299');
    $vid = DB::insert('verification_requests', [
        'user_id' => $uid,
        'tier'    => $tier,
        'amount'  => $amount,
        'status'  => $amount > 0 ? 'pending_payment' : 'pending_upload',
    ]);
    if ($amount > 0) {
        redirect('/checkout/verification/' . (int)$vid);
    }
    flash('success', 'Verification started — please submit your documents below.');
    redirect('/verification');
});

// Document submission — govt ID (+ live selfie photo/video for the selfie tier).
// Allowed while awaiting upload, and again after a rejection (resubmission).
$r->post('/verification/{id}/documents', function ($a) {
    Auth::require();
    $uid = Auth::id();
    $req = DB::one('SELECT * FROM verification_requests WHERE id = ? AND user_id = ?', [(int)$a['id'], $uid]);
    if (!$req || !in_array($req['status'], ['pending_upload', 'rejected'], true)) {
        flash('error', 'This verification request is not open for document submission.');
        redirect('/verification');
    }

    $cfg = $GLOBALS['CFG']['uploads'];
    $dir = $cfg['verify_dir'] . '/' . $uid;

    $idDocTypes = ['aadhaar','pan','passport','driving_licence','voter_id'];
    $idDocType = in_array($_POST['id_doc_type'] ?? '', $idDocTypes, true) ? $_POST['id_doc_type'] : null;
    if (!$idDocType) { flash('error', 'Please select which government ID you are submitting.'); redirect('/verification'); }

    if (empty($_FILES['id_doc']['name']) || $_FILES['id_doc']['error'] !== UPLOAD_ERR_OK) {
        flash('error', 'Please attach a clear photo or PDF of your government ID.');
        redirect('/verification');
    }
    $idPath = store_verification_upload($_FILES['id_doc'], $dir, 'id',
        ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'],
        (int)$cfg['max_bytes']);
    if (!$idPath) { flash('error', 'Government ID must be a JPG, PNG, WEBP or PDF under 4MB.'); redirect('/verification'); }

    $selfiePath = null; $selfieIsVideo = 0;
    if ($req['tier'] === 'selfie') {
        if (empty($_FILES['selfie']['name']) || $_FILES['selfie']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Please capture a live selfie photo or short video.');
            redirect('/verification');
        }
        $selfiePath = store_verification_upload($_FILES['selfie'], $dir, 'selfie',
            ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'video/webm' => 'webm', 'video/mp4' => 'mp4'],
            (int)$cfg['verify_video_max_bytes']);
        if (!$selfiePath) { flash('error', 'Selfie must be a JPG/PNG/WEBP photo or a WEBM/MP4 clip under 15MB.'); redirect('/verification'); }
        $selfieIsVideo = (int) (bool) preg_match('/\.(webm|mp4)$/', $selfiePath);
    }

    // Replace any previous submission's files (resubmission after rejection).
    foreach (['id_doc_path' => $idPath, 'selfie_path' => $selfiePath] as $col => $new) {
        if ($new && !empty($req[$col]) && $req[$col] !== $new) {
            @unlink($cfg['verify_dir'] . '/' . $req[$col]);
        }
    }

    DB::update('verification_requests', [
        'id_doc_type'     => $idDocType,
        'id_doc_path'     => $idPath,
        'selfie_path'     => $selfiePath,
        'selfie_is_video' => $selfieIsVideo,
        'status'          => 'pending_review',
        'reject_reason'   => null,
        'submitted_at'    => date('Y-m-d H:i:s'),
    ], ['id' => $req['id']]);

    flash('success', 'Documents submitted. Our team will review them within 24–48 hours.');
    redirect('/verification');
});

// Stream a verification document to its owner (admins use /admin/verification/{id}/media/{kind}).
// Documents live outside public/ so this authenticated route is the only way in.
$r->get('/verification/{id}/media/{kind}', function ($a) {
    Auth::require();
    $req = DB::one('SELECT * FROM verification_requests WHERE id = ? AND user_id = ?', [(int)$a['id'], Auth::id()]);
    stream_verification_media($req, $a['kind']);
});

// ------------------- BOOST (consume plan boost) -------------------
$r->post('/boost', function () {
    Auth::require();
    if (!consume_plan_boost(Auth::id())) {
        flash('error', 'You have no plan boosts left this month. Upgrade or buy the Profile Boost add-on.');
        redirect('/addons');
    }
    flash('success', 'Profile boosted for 7 days — you will appear higher in search results.');
    redirect('/dashboard');
});

// ------------------- UNLOCK CONTACT -------------------
$r->post('/member/{id}/unlock-contact', function ($a) {
    Auth::require();
    $me = Auth::id();
    $targetId = (int) $a['id'];
    if ($me === $targetId) redirect('/member/' . $targetId);
    $plan = current_plan($me);
    if (!plan_can($plan, 'view_contacts')) {
        flash('error', 'Contact details are a premium feature. Upgrade to unlock.');
        redirect('/packages');
    }
    // Already unlocked? just bounce back.
    $sub = current_subscription($me);
    $already = contact_unlocked($me, $targetId);
    if (!$already) {
        $left = contacts_left($me, $plan, $sub);
        if ($left !== null && $left <= 0) {
            flash('error', 'You have reached your contact unlock limit. Upgrade your plan to view more contact details.');
            redirect('/packages');
        }
        DB::insert('contact_views', [
            'viewer_user_id' => $me,
            'viewed_user_id' => $targetId,
            'subscription_id'=> $sub['id'] ?? null,
        ]);
    }
    redirect('/member/' . $targetId);
});

// ------------------- CHECKOUT (Razorpay) -------------------
// One entrypoint per purchase kind so notes/receipts stay honest.
$r->get('/checkout/{id}', function ($a) { checkout_start('package', (int)$a['id']); });
$r->get('/checkout/addon/{id}', function ($a) { checkout_start('addon', (int)$a['id']); });
$r->get('/checkout/verification/{id}', function ($a) { checkout_start('verification', (int)$a['id']); });

function checkout_start(string $kind, int $itemId): void {
    Auth::require();

    // Resolve the item + amount for this purchase kind.
    $item = null; $amount = 0.0; $label = ''; $notes = ['user_id' => (string) Auth::id(), 'kind' => $kind, 'item_id' => (string)$itemId];
    if ($kind === 'package') {
        $item = DB::one('SELECT * FROM packages WHERE id = ? AND is_active = 1', [$itemId]);
        if (!$item) { http_response_code(404); view('errors/404'); return; }
        if ((float)$item['price'] <= 0) {
            flash('success', 'You are already on the free plan.');
            redirect('/dashboard');
        }
        $amount = (float) $item['price'];
        $label = $item['name'];
    } elseif ($kind === 'addon') {
        $item = DB::one('SELECT * FROM addons WHERE id = ? AND is_active = 1', [$itemId]);
        if (!$item) { http_response_code(404); view('errors/404'); return; }
        $amount = (float) $item['price'];
        $label = $item['name'];
    } else { // verification
        $item = DB::one('SELECT * FROM verification_requests WHERE id = ? AND user_id = ?', [$itemId, Auth::id()]);
        if (!$item) { http_response_code(404); view('errors/404'); return; }
        $amount = (float) $item['amount'];
        $label = $item['tier'] === 'selfie' ? 'Selfie + Identity Verification' : 'Identity Verification';
    }

    $enabled = setting('razorpay_enabled') === '1';
    $keyId   = setting('razorpay_key_id');
    $secret  = setting('razorpay_key_secret');
    if (!$enabled || !$keyId || !$secret) {
        flash('error', 'Online payment is currently unavailable. Please use UPI/bank details on /payment-details.');
        redirect('/payment-details');
    }

    $amountPaise = (int) round($amount * 100);
    $receipt = 'spm_' . Auth::id() . '_' . time();
    $payload = [
        'amount'   => $amountPaise,
        'currency' => 'INR',
        'receipt'  => $receipt,
        'notes'    => $notes,
    ];

    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_USERPWD        => $keyId . ':' . $secret,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 30,
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http !== 200 || !$resp) {
        flash('error', 'Could not start checkout. Please try again or use UPI/bank transfer.');
        redirect($kind === 'package' ? '/packages' : ($kind === 'addon' ? '/addons' : '/verification'));
    }
    $order = json_decode($resp, true);
    if (empty($order['id'])) {
        flash('error', 'Razorpay order creation failed. Please try again.');
        redirect($kind === 'package' ? '/packages' : ($kind === 'addon' ? '/addons' : '/verification'));
    }

    DB::insert('payments', [
        'user_id'          => Auth::id(),
        'package_id'       => $kind === 'package' ? (int)$item['id'] : null,
        'purchase_type'    => $kind,
        'item_id'          => (int)($item['id'] ?? 0),
        'gateway'          => 'razorpay',
        'gateway_order_id' => $order['id'],
        'amount'           => $amount,
        'currency'         => 'INR',
        'status'           => 'created',
        'notes'            => json_encode($payload['notes']),
    ]);

    view('checkout/razorpay', [
        'pkg'      => [
            'name' => $label,
            'price' => $amount,
            'currency' => 'INR',
            'duration_days' => (int)($item['duration_days'] ?? 0),
            'tagline' => $item['tagline'] ?? ($kind === 'verification' ? 'Independent trust verification' : ''),
        ],
        'order'    => $order,
        'key_id'   => $keyId,
        'user'     => Auth::user(),
    ], null);
}

$r->post('/checkout/verify', function () {
    Auth::require();
    $orderId   = $_POST['razorpay_order_id']   ?? '';
    $paymentId = $_POST['razorpay_payment_id'] ?? '';
    $signature = $_POST['razorpay_signature']  ?? '';

    if (!$orderId || !$paymentId || !$signature) {
        flash('error', 'Missing payment details. If money was deducted, contact support — we will activate manually.');
        redirect('/checkout/failed');
    }

    $secret = setting('razorpay_key_secret');
    $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);

    $pmt = DB::one('SELECT * FROM payments WHERE gateway_order_id = ? AND user_id = ?', [$orderId, Auth::id()]);
    if (!$pmt) {
        flash('error', 'Payment record not found.');
        redirect('/checkout/failed');
    }

    if (!hash_equals($expected, $signature)) {
        DB::update('payments', [
            'gateway_payment_id' => $paymentId,
            'gateway_signature'  => $signature,
            'status'             => 'failed',
        ], ['id' => $pmt['id']]);
        flash('error', 'Payment signature mismatch — payment marked failed for safety. If money was deducted, contact support.');
        redirect('/checkout/failed');
    }

    // Idempotent: if webhook fired first, don't double-activate.
    if ($pmt['status'] === 'paid') {
        redirect('/checkout/success?p=' . (int)$pmt['id']);
    }

    // Fulfil the right kind of purchase.
    grant_purchase_by_payment($pmt, $paymentId);

    DB::update('payments', [
        'gateway_payment_id' => $paymentId,
        'gateway_signature'  => $signature,
        'status'             => 'paid',
    ], ['id' => $pmt['id']]);

    redirect('/checkout/success?p=' . (int)$pmt['id']);
});

// Fulfilment fan-out — kept here so /razorpay/webhook and /checkout/verify
// agree on what "paid" means for each purchase type.
function grant_purchase_by_payment(array $pmt, string $paymentId): void {
    $kind = $pmt['purchase_type'] ?? 'package';
    if ($kind === 'package') {
        $pkg = DB::one('SELECT * FROM packages WHERE id = ?', [$pmt['package_id']]);
        if ($pkg) {
            $subId = activate_membership((int)$pmt['user_id'], $pkg, $paymentId, (float)$pmt['amount'], (int)$pmt['id']);
            DB::update('payments', ['subscription_id' => $subId], ['id' => $pmt['id']]);
        }
    } elseif ($kind === 'addon') {
        $addon = DB::one('SELECT * FROM addons WHERE id = ?', [$pmt['item_id']]);
        if ($addon) activate_addon((int)$pmt['user_id'], $addon, (int)$pmt['id']);
    } elseif ($kind === 'verification') {
        // Paid — now waiting on the member to submit their govt ID / live selfie.
        DB::update('verification_requests', [
            'status'     => 'pending_upload',
            'payment_id' => $pmt['id'],
        ], ['id' => $pmt['item_id']]);
    }
}

$r->get('/checkout/success', function () {
    Auth::require();
    $pid = (int)($_GET['p'] ?? 0);
    $pmt = DB::one('SELECT p.*, pk.name AS package_name, pk.duration_days
                    FROM payments p LEFT JOIN packages pk ON pk.id = p.package_id
                    WHERE p.id = ? AND p.user_id = ?', [$pid, Auth::id()]);
    // Enrich the success view with the right label + follow-up CTA for addons / verification.
    if ($pmt) {
        if (($pmt['purchase_type'] ?? 'package') === 'addon') {
            $addon = DB::one('SELECT * FROM addons WHERE id = ?', [$pmt['item_id']]);
            if ($addon) { $pmt['package_name'] = $addon['name']; $pmt['duration_days'] = (int)$addon['duration_days']; }
        } elseif (($pmt['purchase_type'] ?? 'package') === 'verification') {
            $v = DB::one('SELECT * FROM verification_requests WHERE id = ?', [$pmt['item_id']]);
            if ($v) { $pmt['package_name'] = $v['tier'] === 'selfie' ? 'Selfie + Identity Verification' : 'Identity Verification'; $pmt['duration_days'] = 0; }
        }
    }
    view('checkout/success', ['pmt' => $pmt]);
});

$r->get('/checkout/failed', function () {
    Auth::require();
    view('checkout/failed');
});

// ------------------- RAZORPAY WEBHOOK -------------------
$r->post('/razorpay/webhook', function () {
    $secret = setting('razorpay_webhook_secret');
    if (!$secret) { http_response_code(400); echo 'webhook secret not configured'; return; }

    $body = file_get_contents('php://input');
    $sig  = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';
    $expected = hash_hmac('sha256', $body, $secret);
    if (!hash_equals($expected, $sig)) {
        http_response_code(401); echo 'invalid signature'; return;
    }

    $event = json_decode($body, true);
    $type  = $event['event'] ?? '';

    if ($type === 'payment.captured') {
        $p = $event['payload']['payment']['entity'] ?? [];
        $orderId   = $p['order_id'] ?? '';
        $paymentId = $p['id'] ?? '';
        $pmt = DB::one('SELECT * FROM payments WHERE gateway_order_id = ?', [$orderId]);
        if ($pmt && $pmt['status'] !== 'paid') {
            grant_purchase_by_payment($pmt, $paymentId);
            DB::update('payments', [
                'gateway_payment_id' => $paymentId,
                'status'             => 'paid',
            ], ['id' => $pmt['id']]);
        }
    } elseif ($type === 'payment.failed') {
        $p = $event['payload']['payment']['entity'] ?? [];
        $orderId = $p['order_id'] ?? '';
        DB::q('UPDATE payments SET status = "failed", gateway_payment_id = ? WHERE gateway_order_id = ? AND status = "created"',
              [$p['id'] ?? '', $orderId]);
    }

    http_response_code(200); echo 'ok';
});

// ------------------- HAPPY STORIES -------------------
$r->get('/happy-stories', function () {
    $stories = DB::all('SELECT * FROM happy_stories ORDER BY is_featured DESC, id DESC');
    view('happy_stories', ['stories' => $stories]);
});

// ------------------- AUTH -------------------
$r->get('/login', function () {
    if (Auth::check()) redirect('/dashboard');
    view('auth/login', [], 'auth');
});

$r->post('/login', function () {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $u = DB::one('SELECT * FROM users WHERE email = ?', [$email]);
    if (!$u || !password_verify($pass, $u['password_hash'])) {
        flash('error', 'Invalid email or password.');
        redirect('/login');
    }
    if ($u['status'] === 'blocked') {
        flash('error', 'Your account is blocked. Please contact support.');
        redirect('/login');
    }
    Auth::login((int)$u['id']);
    redirect($u['role'] === 'admin' ? '/admin' : '/dashboard');
});

$r->get('/register', function () {
    if (Auth::check()) redirect('/dashboard');
    view('auth/register', [], 'auth');
});

$r->post('/register', function () {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $pass   = $_POST['password'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob    = $_POST['dob'] ?? '';

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6
            || !in_array($gender, ['male','female']) || !$dob) {
        flash('error', 'Please fill all fields. Password must be at least 6 characters.');
        redirect('/register');
    }
    // Legal consent — client-side checkbox can be bypassed, so re-check here.
    if (empty($_POST['agree'])) {
        flash('error', 'Please accept the Terms, Privacy Policy, Refund Policy and Cookie Policy to continue.');
        redirect('/register');
    }
    if (DB::val('SELECT 1 FROM users WHERE email = ?', [$email])) {
        flash('error', 'An account with this email already exists.');
        redirect('/register');
    }
    $_SESSION['pending_signup'] = [
        'name'          => $name,
        'email'         => $email,
        'phone'         => $phone,
        'password_hash' => password_hash($pass, PASSWORD_BCRYPT),
        'gender'          => $gender,
        'dob'             => $dob,
        'created_at'      => time(),
    ];
    $_SESSION['signup_otp_email'] = $email;
    if (!issue_signup_otp($_SESSION['pending_signup'])) {
        clear_signup_otp_session();
        flash('error', 'Could not send the verification email. Please check SMTP settings or contact support.');
        redirect('/register');
    }
    $_SESSION['signup_otp_last_sent'] = time();
    flash('success', 'We sent a 6-digit code to your email. Enter it to create your account.');
    redirect('/verify-signup-otp');
});

$r->get('/verify-signup-otp', function () {
    if (Auth::check()) redirect('/dashboard');
    if (empty($_SESSION['pending_signup']['email'])) redirect('/register');
    view('auth/verify_otp', [
        'email' => $_SESSION['pending_signup']['email'],
        'mode' => 'signup',
    ], 'auth');
});

$r->post('/verify-signup-otp', function () {
    if (empty($_SESSION['pending_signup']['email'])) redirect('/register');
    $pending = $_SESSION['pending_signup'];
    $email   = $pending['email'];
    $otp     = preg_replace('/\D/', '', $_POST['otp'] ?? '');

    if (strlen($otp) !== 6) {
        flash('error', 'Please enter the 6-digit code from your email.');
        redirect('/verify-signup-otp');
    }
    if (time() - (int)($pending['created_at'] ?? 0) > 1800) {
        clear_signup_otp_session();
        flash('error', 'Your signup verification expired. Please register again.');
        redirect('/register');
    }
    if (DB::val('SELECT 1 FROM users WHERE email = ?', [$email])) {
        clear_signup_otp_session();
        flash('error', 'An account with this email already exists. Please sign in.');
        redirect('/login');
    }

    $row = latest_signup_otp($email);
    if (!$row) {
        flash('error', 'Invalid or expired code. Please request a new one.');
        redirect('/verify-signup-otp');
    }
    if ((int) $row['attempts'] >= 5) {
        DB::update('signup_otps', ['used_at' => date('Y-m-d H:i:s')], ['id' => $row['id']]);
        flash('error', 'Too many wrong attempts. Please request a new code.');
        redirect('/verify-signup-otp');
    }
    if (!hash_equals($row['otp_hash'], hash('sha256', $otp))) {
        DB::q('UPDATE signup_otps SET attempts = attempts + 1 WHERE id = ?', [$row['id']]);
        flash('error', 'Invalid code. ' . (4 - (int) $row['attempts']) . ' attempts left.');
        redirect('/verify-signup-otp');
    }

    DB::update('signup_otps', ['used_at' => date('Y-m-d H:i:s')], ['id' => $row['id']]);
    DB::q('UPDATE signup_otps SET used_at = NOW() WHERE email = ? AND used_at IS NULL', [$email]);

    $uid = DB::insert('users', [
        'name'              => $pending['name'],
        'email'             => $email,
        'phone'             => $pending['phone'],
        'password_hash'     => $pending['password_hash'],
        'role'              => 'member',
        'status'            => 'active',
        'email_verified_at' => date('Y-m-d H:i:s'),
    ]);
    DB::insert('profiles', [
        'user_id'          => $uid,
        'gender'           => $pending['gender'],
        'dob'              => $pending['dob'],
        'profile_complete' => 0,
    ]);

    clear_signup_otp_session();
    Auth::login($uid);
    flash('success', 'Welcome! Complete your profile so others can find you.');
    redirect('/profile/edit');
});

$r->post('/resend-signup-otp', function () {
    if (empty($_SESSION['pending_signup']['email'])) redirect('/register');
    $last = $_SESSION['signup_otp_last_sent'] ?? 0;
    if (time() - $last < 60) {
        flash('error', 'Please wait a minute before requesting another code.');
        redirect('/verify-signup-otp');
    }
    if (DB::val('SELECT 1 FROM users WHERE email = ?', [$_SESSION['pending_signup']['email']])) {
        clear_signup_otp_session();
        flash('error', 'An account with this email already exists. Please sign in.');
        redirect('/login');
    }
    if (!issue_signup_otp($_SESSION['pending_signup'])) {
        flash('error', 'Could not send the verification email. Please check SMTP settings or contact support.');
        redirect('/verify-signup-otp');
    }
    $_SESSION['signup_otp_last_sent'] = time();
    flash('success', 'A fresh signup code has been sent.');
    redirect('/verify-signup-otp');
});

$r->post('/logout', function () {
    Auth::logout();
    redirect('/');
});
// also allow GET for convenience
$r->get('/logout', function () { Auth::logout(); redirect('/'); });

// ------------------- FORGOT / RESET PASSWORD (Email OTP) -------------------
$r->get('/forgot-password', function () {
    if (Auth::check()) redirect('/dashboard');
    view('auth/forgot', [], 'auth');
});

$r->post('/forgot-password', function () {
    $email = trim($_POST['email'] ?? '');
    $generic = 'If an account exists for that email, we have sent a 6-digit code. Please check your inbox (and spam folder).';

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $u = DB::one('SELECT id, name, email FROM users WHERE email = ? AND status != "blocked"', [$email]);
        if ($u && !issue_password_otp($u)) {
            flash('error', 'Could not send the verification email. Please check SMTP settings or contact support.');
            redirect('/forgot-password');
        }
    }
    // Carry the email into the verify step regardless, so the response is identical
    // whether the email exists or not — avoids leaking which emails are registered.
    $_SESSION['otp_email'] = $email;
    flash('success', $generic);
    redirect('/verify-otp');
});

$r->get('/verify-otp', function () {
    if (Auth::check()) redirect('/dashboard');
    if (empty($_SESSION['otp_email'])) redirect('/forgot-password');
    view('auth/verify_otp', ['email' => $_SESSION['otp_email']], 'auth');
});

$r->post('/verify-otp', function () {
    if (empty($_SESSION['otp_email'])) redirect('/forgot-password');
    $email = $_SESSION['otp_email'];
    $otp   = preg_replace('/\D/', '', $_POST['otp'] ?? '');

    if (strlen($otp) !== 6) {
        flash('error', 'Please enter the 6-digit code from your email.');
        redirect('/verify-otp');
    }

    $u = DB::one('SELECT id, name, email FROM users WHERE email = ? AND status != "blocked"', [$email]);
    if (!$u) {
        // Burn a moment of time even when the user doesn't exist, then show the same generic error
        // a real wrong-code attempt would show — avoids being a probe oracle.
        usleep(200000);
        flash('error', 'Invalid or expired code. Please request a new one.');
        redirect('/verify-otp');
    }

    $row = DB::one(
        'SELECT * FROM password_resets
         WHERE user_id = ? AND otp_hash IS NOT NULL AND used_at IS NULL AND expires_at > NOW()
         ORDER BY id DESC LIMIT 1',
        [$u['id']]
    );

    if (!$row) {
        flash('error', 'Invalid or expired code. Please request a new one.');
        redirect('/verify-otp');
    }
    if ((int) $row['attempts'] >= 5) {
        DB::update('password_resets', ['used_at' => date('Y-m-d H:i:s')], ['id' => $row['id']]);
        flash('error', 'Too many wrong attempts. Please request a new code.');
        redirect('/forgot-password');
    }

    if (!hash_equals($row['otp_hash'], hash('sha256', $otp))) {
        DB::q('UPDATE password_resets SET attempts = attempts + 1 WHERE id = ?', [$row['id']]);
        flash('error', 'Invalid code. ' . (4 - (int) $row['attempts']) . ' attempts left.');
        redirect('/verify-otp');
    }

    // Success: consume the OTP and authorize a short reset window via session.
    DB::update('password_resets', ['used_at' => date('Y-m-d H:i:s')], ['id' => $row['id']]);
    DB::q('UPDATE password_resets SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL', [$u['id']]);
    $_SESSION['reset_authorized_user_id'] = (int) $u['id'];
    $_SESSION['reset_authorized_until']   = time() + 600; // 10 minutes to choose a password
    unset($_SESSION['otp_email']);
    redirect('/reset-password');
});

$r->post('/resend-otp', function () {
    if (empty($_SESSION['otp_email'])) redirect('/forgot-password');
    $last = $_SESSION['otp_last_sent'] ?? 0;
    if (time() - $last < 60) {
        flash('error', 'Please wait a minute before requesting another code.');
        redirect('/verify-otp');
    }
    $u = DB::one('SELECT id, name, email FROM users WHERE email = ? AND status != "blocked"', [$_SESSION['otp_email']]);
    if ($u && !issue_password_otp($u)) {
        flash('error', 'Could not send the verification email. Please check SMTP settings or contact support.');
        redirect('/verify-otp');
    }
    $_SESSION['otp_last_sent'] = time();
    flash('success', 'If your email is registered, a fresh code has been sent.');
    redirect('/verify-otp');
});

$r->get('/reset-password/{token}', function ($a) {
    if (Auth::check()) redirect('/dashboard');
    $token = $a['token'] ?? '';
    if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
        flash('error', 'Invalid or expired reset link. Please request a new one.');
        redirect('/forgot-password');
    }

    $row = DB::one(
        'SELECT * FROM password_resets
         WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()
         ORDER BY id DESC LIMIT 1',
        [hash('sha256', $token)]
    );
    if (!$row) {
        flash('error', 'Invalid or expired reset link. Please request a new one.');
        redirect('/forgot-password');
    }

    DB::update('password_resets', ['used_at' => date('Y-m-d H:i:s')], ['id' => $row['id']]);
    $_SESSION['reset_authorized_user_id'] = (int) $row['user_id'];
    $_SESSION['reset_authorized_until'] = time() + 600;
    redirect('/reset-password');
});

$r->get('/reset-password', function () {
    if (!reset_session_valid()) {
        flash('error', 'Your verification has expired. Please start again.');
        redirect('/forgot-password');
    }
    view('auth/reset', [], 'auth');
});

$r->post('/reset-password', function () {
    if (!reset_session_valid()) {
        flash('error', 'Your verification has expired. Please start again.');
        redirect('/forgot-password');
    }
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';
    if (strlen($pass) < 6) {
        flash('error', 'Password must be at least 6 characters.');
        redirect('/reset-password');
    }
    if ($pass !== $confirm) {
        flash('error', 'Passwords do not match.');
        redirect('/reset-password');
    }

    $uid = (int) $_SESSION['reset_authorized_user_id'];
    DB::update('users', ['password_hash' => password_hash($pass, PASSWORD_BCRYPT)], ['id' => $uid]);
    unset($_SESSION['reset_authorized_user_id'], $_SESSION['reset_authorized_until'], $_SESSION['otp_last_sent']);

    flash('success', 'Password updated. Please sign in with your new password.');
    redirect('/login');
});

// Issues a fresh OTP for the user, stores its hash, and sends the email.
function issue_password_otp(array $user): bool {
    $otp  = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash = hash('sha256', $otp);
    DB::q(
        'UPDATE password_resets SET used_at = NOW()
         WHERE user_id = ? AND otp_hash IS NOT NULL AND used_at IS NULL',
        [$user['id']]
    );
    DB::insert('password_resets', [
        'user_id'      => $user['id'],
        'otp_hash'     => $hash,
        'expires_at'   => date('Y-m-d H:i:s', strtotime('+10 minutes')),
        'requested_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
    return send_password_reset_email($user, $otp);
}

function issue_signup_otp(array $pending): bool {
    DB::q(
        'UPDATE signup_otps SET used_at = NOW()
         WHERE email = ? AND used_at IS NULL',
        [$pending['email']]
    );
    $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    DB::insert('signup_otps', [
        'email'        => $pending['email'],
        'otp_hash'     => hash('sha256', $otp),
        'expires_at'   => date('Y-m-d H:i:s', strtotime('+10 minutes')),
        'requested_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
    return send_signup_otp_email($pending, $otp);
}

function latest_signup_otp(string $email): ?array {
    return DB::one(
        'SELECT * FROM signup_otps
         WHERE email = ? AND used_at IS NULL AND expires_at > NOW()
         ORDER BY id DESC LIMIT 1',
        [$email]
    );
}

function clear_signup_otp_session(): void {
    unset($_SESSION['pending_signup'], $_SESSION['signup_otp_email'], $_SESSION['signup_otp_last_sent']);
}

function reset_session_valid(): bool {
    return !empty($_SESSION['reset_authorized_user_id'])
        && !empty($_SESSION['reset_authorized_until'])
        && time() < (int) $_SESSION['reset_authorized_until'];
}

// Helper: send the OTP email via PHP mail(). Best-effort — fails silently if mail() is unavailable.
function send_password_reset_email(array $user, string $otp): bool {
    $siteName = setting('site_name', 'Spiritual Matrimony');
    $replyTo = setting('contact_email', $GLOBALS['CFG']['mail']['from'] ?? null);

    $subject = "Your {$siteName} password reset code";
    $body = "Namaste {$user['name']},\n\n"
          . "Your password reset code is:\n\n"
          . "    {$otp}\n\n"
          . "This code is valid for 10 minutes and can be used only once. Do not share it with anyone — our team will never ask for it.\n\n"
          . "If you did not request this, simply ignore this email — your password will not change.\n\n"
          . "With sincerity,\n"
          . $siteName . " team\n";

    return send_transactional_mail($user['email'], $subject, $body, $replyTo);
}

function send_signup_otp_email(array $pending, string $otp): bool {
    $siteName = setting('site_name', 'Spiritual Matrimony');
    $replyTo = setting('contact_email', $GLOBALS['CFG']['mail']['from'] ?? null);

    $subject = "Your {$siteName} signup code";
    $body = "Namaste {$pending['name']},\n\n"
          . "Your signup verification code is:\n\n"
          . "    {$otp}\n\n"
          . "This code is valid for 10 minutes and can be used only once. Do not share it with anyone.\n\n"
          . "If you did not request this, you can ignore this email.\n\n"
          . "With sincerity,\n"
          . $siteName . " team\n";

    return send_transactional_mail($pending['email'], $subject, $body, $replyTo);
}

// ------------------- MEMBER DASHBOARD -------------------
$r->get('/dashboard', function () {
    Auth::require();
    $uid = Auth::id();
    $stats = [
        'interests_received' => DB::val('SELECT COUNT(*) FROM interests WHERE receiver_id = ? AND status = "sent"', [$uid]),
        'interests_accepted' => DB::val('SELECT COUNT(*) FROM interests WHERE (sender_id = ? OR receiver_id = ?) AND status = "accepted"', [$uid,$uid]),
        'shortlisted'        => DB::val('SELECT COUNT(*) FROM shortlists WHERE user_id = ?', [$uid]),
        'profile_views'      => DB::val('SELECT views FROM profiles WHERE user_id = ?', [$uid]) ?: 0,
        'shortlisted_me'     => DB::val('SELECT COUNT(*) FROM shortlists WHERE target_user_id = ?', [$uid]),
    ];
    $membership = membership_summary($uid);
    $me = DB::one('SELECT * FROM profiles WHERE user_id = ?', [$uid]);
    $opp = opposite_gender($me['gender'] ?? null);
    // We intentionally do NOT filter by profile_complete: seekers should discover each
    // other as soon as they register. Cards show an "in progress" badge for incomplete
    // profiles, and actions (view/contact) prompt the viewer to finish their own bio first.
    $matchWhere = "u.id != :uid AND u.status = 'active' AND u.role = 'member'";
    $matchParams = ['uid' => $uid];
    if ($opp) {
        $matchWhere .= ' AND p.gender = :gender';
        $matchParams['gender'] = $opp;
    }
    $priorityJoin = profile_priority_join_sql();
    $prioritySelect = profile_priority_select_sql();
    $matches = DB::all("SELECT u.*, p.gender, p.dob, p.city, p.profession, p.about_me, p.height_cm, p.profile_complete, p.verified_tier, s.spiritual_path,
                               {$prioritySelect}
                        FROM users u
                        JOIN profiles p ON p.user_id = u.id
                   LEFT JOIN spiritual_details s ON s.user_id = u.id
                   {$priorityJoin}
                       WHERE {$matchWhere}
                    ORDER BY " . profile_priority_order_sql('u.created_at DESC') . " LIMIT 6", $matchParams);
    $recent_interests = DB::all("SELECT i.*, u.name, u.id AS uid, p.city, p.profession, p.dob
                                   FROM interests i
                                   JOIN users u ON u.id = i.sender_id
                              LEFT JOIN profiles p ON p.user_id = u.id
                                  WHERE i.receiver_id = ?
                                    AND u.role = 'member' AND u.status = 'active'
                               ORDER BY i.created_at DESC LIMIT 5", [$uid]);
    view('member/dashboard', compact('stats','matches','recent_interests','membership'));
});

// ------------------- BILLING (member payment history) -------------------
$r->get('/billing', function () {
    Auth::require();
    $uid = Auth::id();
    $membership = membership_summary($uid);
    $payments = DB::all("SELECT p.*,
                                COALESCE(
                                    pk.name,
                                    ad.name,
                                    CASE
                                        WHEN vr.tier = 'selfie'   THEN 'Selfie + Identity Verification'
                                        WHEN vr.tier = 'identity' THEN 'Identity Verification'
                                        ELSE 'Purchase'
                                    END
                                ) AS item_name
                           FROM payments p
                      LEFT JOIN packages pk ON pk.id = p.package_id
                      LEFT JOIN addons   ad ON ad.id = p.item_id AND p.purchase_type = 'addon'
                      LEFT JOIN verification_requests vr ON vr.id = p.item_id AND p.purchase_type = 'verification'
                          WHERE p.user_id = ?
                       ORDER BY p.created_at DESC
                          LIMIT 200", [$uid]);
    $subs = DB::all("SELECT s.*, pk.name AS package_name, pk.slug AS package_slug
                       FROM subscriptions s
                       JOIN packages pk ON pk.id = s.package_id
                      WHERE s.user_id = ?
                   ORDER BY s.starts_at DESC LIMIT 50", [$uid]);
    view('member/billing', compact('payments','subs','membership'));
});

$r->get('/visitors', function () {
    Auth::require();
    $plan = current_plan(Auth::id());
    if (!plan_can($plan, 'see_who_viewed')) {
        flash('error', 'Profile visitors are available from Starter Premium and higher.');
        redirect('/packages');
    }
    $rows = DB::all("SELECT u.id, u.name, p.dob, p.city, p.profession, p.verified_tier,
                            MAX(v.created_at) AS last_viewed_at,
                            COUNT(*) AS view_count
                       FROM profile_views_log v
                       JOIN users u ON u.id = v.viewer_user_id
                       JOIN profiles p ON p.user_id = u.id
                      WHERE v.viewed_user_id = ?
                        AND v.viewer_user_id != ?
                        AND u.role = 'member' AND u.status = 'active'
                   GROUP BY u.id, u.name, p.dob, p.city, p.profession, p.verified_tier
                   ORDER BY last_viewed_at DESC
                      LIMIT 100", [Auth::id(), Auth::id()]);
    view('member/visitors', ['rows' => $rows]);
});

$r->get('/shortlisted-by', function () {
    Auth::require();
    $plan = current_plan(Auth::id());
    if (!plan_can($plan, 'see_who_shortlisted')) {
        flash('error', 'Who shortlisted you is available from Divine Plus and higher.');
        redirect('/packages');
    }
    $rows = DB::all("SELECT u.id, u.name, p.dob, p.city, p.profession, p.about_me, p.verified_tier,
                            sh.created_at AS shortlisted_at
                       FROM shortlists sh
                       JOIN users u ON u.id = sh.user_id
                       JOIN profiles p ON p.user_id = u.id
                      WHERE sh.target_user_id = ?
                        AND u.role = 'member' AND u.status = 'active'
                   ORDER BY sh.created_at DESC
                      LIMIT 100", [Auth::id()]);
    view('member/shortlisted_by', ['rows' => $rows]);
});

// ------------------- PROFILE EDIT -------------------
$r->get('/profile/edit', function () {
    Auth::require();
    $uid = Auth::id();
    $profile = DB::one('SELECT * FROM profiles WHERE user_id = ?', [$uid]);
    $spiritual = DB::one('SELECT * FROM spiritual_details WHERE user_id = ?', [$uid]);
    $horoscope = DB::one('SELECT * FROM horoscopes WHERE user_id = ?', [$uid]);
    // Always compute missing fields, not just when bounced from Express Interest.
    // Users who save a partial profile and browse away otherwise have no signal
    // that Express Interest is still gated on them.
    $missing = profile_missing_fields($uid);
    $photoCount = (int) DB::val('SELECT COUNT(*) FROM photos WHERE user_id = ?', [$uid]);
    view('profile/edit', compact('profile','spiritual','horoscope','missing','photoCount'));
});

$r->post('/profile/edit', function () {
    Auth::require();
    $uid = Auth::id();
    $allowed = ['gender','dob','height_cm','marital_status','mother_tongue','religion','community','caste','gotra','manglik',
                'country','state','city','education','profession','annual_income','family_type','family_status','diet','about_me','partner_pref'];
    $data = [];
    foreach ($allowed as $k) {
        if (isset($_POST[$k])) $data[$k] = $_POST[$k] === '' ? null : $_POST[$k];
    }
    $profileExists = DB::val('SELECT id FROM profiles WHERE user_id = ?', [$uid]);
    if ($profileExists) {
        DB::update('profiles', $data, ['user_id' => $uid]);
    } else {
        $data['user_id'] = $uid;
        DB::insert('profiles', $data);
    }

    // also update user name
    if (!empty($_POST['name'])) {
        DB::update('users', ['name' => trim($_POST['name'])], ['id' => $uid]);
    }

    // Completeness now depends on photos too, so recompute after the write.
    recompute_profile_complete($uid);

    flash('success', profile_save_flash($uid, 'Profile saved'));
    redirect('/profile/edit');
});

// ------------------- SPIRITUAL DETAILS -------------------
$r->post('/profile/spiritual', function () {
    Auth::require();
    $uid = Auth::id();
    $fields = [
        'spiritual_path','guru','ishta_devata','daily_sadhana','favorite_scripture','fasting_practice','pilgrimage_done','mantra',
        'spiritual_organization','temple_visit_frequency','vegetarian','vegan','no_smoking','no_alcohol',
        'scripture_preference','festival_participation','spiritual_lifestyle',
    ];
    $data = ['user_id' => $uid];
    foreach ($fields as $k) {
        $data[$k] = in_array($k, ['vegetarian','vegan','no_smoking','no_alcohol'], true)
            ? (isset($_POST[$k]) ? 1 : 0)
            : ($_POST[$k] ?? null);
    }

    $exists = DB::val('SELECT id FROM spiritual_details WHERE user_id = ?', [$uid]);
    if ($exists) {
        unset($data['user_id']);
        DB::update('spiritual_details', $data, ['user_id' => $uid]);
    } else {
        DB::insert('spiritual_details', $data);
    }
    flash('success', profile_save_flash($uid, 'Spiritual details saved'));
    redirect('/profile/edit#spiritual');
});

// ------------------- PHOTOS -------------------
$r->get('/profile/photos', function () {
    Auth::require();
    $uid = Auth::id();
    $photos = DB::all('SELECT * FROM photos WHERE user_id = ? ORDER BY is_primary DESC, id', [$uid]);
    // Also compute what's missing on the bio side, so a user sitting on the
    // gallery page sees the full "what still blocks Express Interest" picture,
    // not just their photo count.
    $missing = profile_missing_fields($uid);
    $photoLimit = profile_photo_limit($uid);
    $plan = current_plan($uid);
    view('profile/photos', compact('photos','missing','photoLimit','plan'));
});

$r->post('/profile/photos', function () {
    Auth::require();
    $cfg = $GLOBALS['CFG']['uploads'];
    $uid = Auth::id();
    $existing = (int) DB::val('SELECT COUNT(*) FROM photos WHERE user_id = ?', [$uid]);
    $photoLimit = profile_photo_limit($uid);
    if ($photoLimit !== null && $existing >= $photoLimit) {
        flash('error','Your current plan allows at most ' . $photoLimit . ' photos. Upgrade for unlimited profile photos.');
        redirect('/profile/photos');
    }
    if (empty($_FILES['photo']['name'])) { flash('error','Choose a photo.'); redirect('/profile/photos'); }
    $f = $_FILES['photo'];
    if ($f['error'] !== UPLOAD_ERR_OK) { flash('error','Upload failed.'); redirect('/profile/photos'); }
    if ($f['size'] > $cfg['max_bytes']) { flash('error','Photo too large (max 4MB).'); redirect('/profile/photos'); }
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $cfg['allowed'])) { flash('error','JPG / PNG / WEBP only.'); redirect('/profile/photos'); }
    if (!is_dir($cfg['avatar_dir'])) mkdir($cfg['avatar_dir'], 0775, true);
    $name = $uid . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    move_uploaded_file($f['tmp_name'], $cfg['avatar_dir'] . '/' . $name);
    $isFirst = $existing === 0 ? 1 : 0;
    DB::insert('photos', ['user_id' => $uid, 'path' => 'avatars/' . $name, 'is_primary' => $isFirst]);
    recompute_profile_complete($uid);
    $nowHave = $existing + 1;
    flash('success', profile_save_flash($uid, 'Photo uploaded (' . $nowHave . ' of ' . PROFILE_PHOTO_MAX . ')'));
    redirect('/profile/photos');
});

$r->post('/profile/photos/{id}/primary', function ($a) {
    Auth::require();
    DB::q('UPDATE photos SET is_primary = 0 WHERE user_id = ?', [Auth::id()]);
    DB::q('UPDATE photos SET is_primary = 1 WHERE id = ? AND user_id = ?', [$a['id'], Auth::id()]);
    redirect('/profile/photos');
});

$r->post('/profile/photos/{id}/delete', function ($a) {
    Auth::require();
    $uid = Auth::id();
    $row = DB::one('SELECT * FROM photos WHERE id = ? AND user_id = ?', [$a['id'], $uid]);
    if ($row) {
        @unlink(__DIR__ . '/../public/uploads/' . $row['path']);
        DB::q('DELETE FROM photos WHERE id = ?', [$row['id']]);
        // If we just removed the primary, promote the oldest remaining photo.
        if ((int)$row['is_primary'] === 1) {
            $next = DB::val('SELECT id FROM photos WHERE user_id = ? ORDER BY id LIMIT 1', [$uid]);
            if ($next) DB::q('UPDATE photos SET is_primary = 1 WHERE id = ?', [$next]);
        }
        recompute_profile_complete($uid);
    }
    redirect('/profile/photos');
});

// ------------------- BROWSE / SEARCH -------------------
$r->get('/browse', function () {
    Auth::require();
    $viewerPlan = current_plan(Auth::id());
    $advancedAllowed = plan_can($viewerPlan, 'advanced_search');
    $advancedKeys = [
        'education','profession','community','guru','organization','temple_frequency',
        'scripture','lifestyle','min_height','max_height','vegetarian','vegan','no_smoking','no_alcohol',
    ];
    $advancedRequested = false;
    foreach ($advancedKeys as $key) {
        if (isset($_GET[$key]) && trim((string)$_GET[$key]) !== '') {
            $advancedRequested = true;
            break;
        }
    }
    if ($advancedRequested && !$advancedAllowed) {
        flash('error', 'Advanced search filters are available from Divine Plus and higher.');
        redirect('/packages');
    }

    $me  = DB::one('SELECT * FROM profiles WHERE user_id = ?', [Auth::id()]);
    $opp = opposite_gender($me['gender'] ?? null);
    // Show every active member — even those still finishing their bio. Cards flag
    // "in progress" profiles and gated actions ask the viewer to complete their own first.
    $where  = ["u.status = 'active'", "u.role = 'member'", 'u.id != :me'];
    $params = ['me' => Auth::id()];

    if ($opp) {
        $where[] = 'p.gender = :g';
        $params['g'] = $opp;
    }

    if (!empty($_GET['city']))     { $where[] = 'p.city LIKE :city';   $params['city']   = '%' . $_GET['city'] . '%'; }
    if (!empty($_GET['religion'])) { $where[] = 'p.religion = :rel';   $params['rel']    = $_GET['religion']; }
    if (!empty($_GET['diet']))     { $where[] = 'p.diet = :diet';      $params['diet']   = $_GET['diet']; }
    if (!empty($_GET['path']))     { $where[] = 's.spiritual_path LIKE :sp'; $params['sp'] = '%' . $_GET['path'] . '%'; }
    if (!empty($_GET['min_age']))  { $where[] = 'TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= :min'; $params['min'] = (int)$_GET['min_age']; }
    if (!empty($_GET['max_age']))  { $where[] = 'TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) <= :max'; $params['max'] = (int)$_GET['max_age']; }

    if ($advancedAllowed) {
        if (!empty($_GET['education']))  { $where[] = 'p.education LIKE :edu';     $params['edu'] = '%' . $_GET['education'] . '%'; }
        if (!empty($_GET['profession'])) { $where[] = 'p.profession LIKE :prof';   $params['prof'] = '%' . $_GET['profession'] . '%'; }
        if (!empty($_GET['community']))  { $where[] = 'p.community LIKE :comm';    $params['comm'] = '%' . $_GET['community'] . '%'; }
        if (!empty($_GET['guru']))       { $where[] = 's.guru LIKE :guru';         $params['guru'] = '%' . $_GET['guru'] . '%'; }
        if (!empty($_GET['organization'])) {
            $where[] = 's.spiritual_organization LIKE :org';
            $params['org'] = '%' . $_GET['organization'] . '%';
        }
        if (!empty($_GET['temple_frequency'])) {
            $where[] = 's.temple_visit_frequency = :temple';
            $params['temple'] = $_GET['temple_frequency'];
        }
        if (!empty($_GET['scripture'])) {
            $where[] = 's.scripture_preference LIKE :scripture';
            $params['scripture'] = '%' . $_GET['scripture'] . '%';
        }
        if (!empty($_GET['lifestyle'])) {
            $where[] = 's.spiritual_lifestyle LIKE :lifestyle';
            $params['lifestyle'] = '%' . $_GET['lifestyle'] . '%';
        }
        if (!empty($_GET['min_height'])) { $where[] = 'p.height_cm >= :min_h'; $params['min_h'] = (int)$_GET['min_height']; }
        if (!empty($_GET['max_height'])) { $where[] = 'p.height_cm <= :max_h'; $params['max_h'] = (int)$_GET['max_height']; }
        foreach (['vegetarian','vegan','no_smoking','no_alcohol'] as $flag) {
            if (!empty($_GET[$flag])) $where[] = "s.`{$flag}` = 1";
        }
    }

    $page = max(1, (int)($_GET['page'] ?? 1));
    $per  = 9;
    $total = (int) DB::val("SELECT COUNT(*) FROM users u JOIN profiles p ON p.user_id = u.id
                            LEFT JOIN spiritual_details s ON s.user_id = u.id
                            WHERE " . implode(' AND ', $where), $params);
    $pg = paginate($total, $per, $page);

    $priorityJoin = profile_priority_join_sql();
    $prioritySelect = profile_priority_select_sql();
    $rows = DB::all("SELECT u.id, u.name, p.dob, p.gender, p.city, p.state, p.country, p.height_cm,
                            p.profession, p.education, p.about_me, p.religion, p.community, p.profile_complete,
                            p.verified_tier, s.spiritual_path, s.guru,
                            {$prioritySelect}
                       FROM users u
                       JOIN profiles p ON p.user_id = u.id
                  LEFT JOIN spiritual_details s ON s.user_id = u.id
                  {$priorityJoin}
                      WHERE " . implode(' AND ', $where) . "
                   ORDER BY " . profile_priority_order_sql('u.created_at DESC') . "
                      LIMIT {$pg['limit']} OFFSET {$pg['offset']}", $params);

    view('browse/index', [
        'rows' => $rows,
        'page' => $pg,
        'total' => $total,
        'viewerPlan' => $viewerPlan,
        'advancedAllowed' => $advancedAllowed,
    ]);
});

// ------------------- MEMBER PROFILE VIEW -------------------
$r->get('/member/{id}', function ($a) {
    Auth::require();
    $targetId = (int) $a['id'];
    if ($targetId === Auth::id()) {
        redirect('/profile/edit');
    }
    // Viewer must finish their own bio before opening someone else's — otherwise the
    // other seeker has nothing to read back if they get curious. Browse can be
    // explored freely; the gate only kicks in when someone tries to *engage*.
    $viewer = active_member_profile(Auth::id());
    if (!$viewer || !(int)($viewer['profile_complete'] ?? 0)) {
        flash('error', 'Please complete your profile first so other seekers can learn about you too.');
        redirect('/profile/edit');
    }
    // p.* first, then u.* — so duplicate keys (id, created_at, updated_at) resolve to the users row.
    // We intentionally do NOT filter on profile_complete here: when someone receives an
    // interest from a member who is still finishing their bio, the receiver should still
    // be able to land on the page (with a soft "still preparing" notice) instead of a 404.
    $u = DB::one("SELECT p.*, u.id, u.name, u.email, u.phone, u.role, u.status, u.created_at, u.updated_at
                    FROM users u JOIN profiles p ON p.user_id = u.id
                   WHERE u.id = ? AND u.role = 'member' AND u.status = 'active'", [$targetId]);
    if (!$u) { http_response_code(404); view('errors/404'); return; }
    $isComplete = (int)($u['profile_complete'] ?? 0) === 1;
    $sp = DB::one('SELECT * FROM spiritual_details WHERE user_id = ?', [$targetId]);
    $photos = DB::all('SELECT * FROM photos WHERE user_id = ? ORDER BY is_primary DESC', [$targetId]);
    DB::q('UPDATE profiles SET views = views + 1 WHERE user_id = ?', [$targetId]);
    DB::insert('profile_views_log', [
        'viewer_user_id' => Auth::id(),
        'viewed_user_id' => $targetId,
    ]);
    $interest = interest_between(Auth::id(), $targetId);
    $shortlisted = (bool) DB::val('SELECT 1 FROM shortlists WHERE user_id = ? AND target_user_id = ?', [Auth::id(), $targetId]);
    $canMessage = $interest && $interest['status'] === 'accepted';
    $viewerPlan = current_plan(Auth::id());
    $contactUnlocked = contact_unlocked(Auth::id(), $targetId);
    $contactsLeft = contacts_left(Auth::id(), $viewerPlan, current_subscription(Auth::id()));
    $targetBadge = membership_badge($targetId);
    $targetFeatured = is_featured($targetId);
    $targetBoosted = is_boosted($targetId);
    view('member/show', compact(
        'u','sp','photos','interest','shortlisted','canMessage','isComplete',
        'viewerPlan','contactUnlocked','contactsLeft','targetBadge','targetFeatured','targetBoosted'
    ));
});

// ------------------- INTERESTS -------------------
$r->post('/interest/send/{id}', function ($a) {
    Auth::require();
    $uid = Auth::id();
    $targetId = (int) $a['id'];
    if ($targetId === $uid) {
        flash('error', 'You cannot express interest in your own profile.');
        redirect('/browse');
    }

    $me = active_member_profile($uid);
    // Use the live check (not the stored flag) so legacy accounts that pre-date
    // the 2-photo requirement can't slip past. The edit/photos pages recompute
    // this themselves, so we don't need to stash — just point the user there.
    $missing = $me ? profile_missing_fields($uid) : profile_required_fields();
    if (!$me || $missing) {
        $labels = array_values($missing);
        $msg = 'Please complete your profile before expressing interest';
        if ($labels) $msg .= ' — still needed: ' . implode(', ', $labels) . '.';
        flash('error', $msg);
        redirect(isset($missing['photos']) && count($missing) === 1 ? '/profile/photos' : '/profile/edit');
    }
    $target = active_member_profile($targetId);
    if (!$target || !(int)($target['profile_complete'] ?? 0)) {
        flash('error', 'This profile is not available for interests right now.');
        redirect('/browse');
    }
    if (!empty($me['gender']) && !empty($target['gender']) && $me['gender'] === $target['gender']) {
        flash('error', 'You can express interest only in compatible profiles.');
        redirect('/member/' . $targetId);
    }

    $accepted = accepted_interest_between($uid, $targetId);
    if ($accepted) {
        flash('success', 'You are already connected. You can message each other now.');
        redirect('/messages/' . $targetId);
    }

    $outbound = DB::one('SELECT * FROM interests WHERE sender_id = ? AND receiver_id = ? ORDER BY id DESC LIMIT 1', [$uid, $targetId]);
    $inbound = DB::one('SELECT * FROM interests WHERE sender_id = ? AND receiver_id = ? ORDER BY id DESC LIMIT 1', [$targetId, $uid]);

    if ($inbound && $inbound['status'] === 'sent') {
        DB::update('interests', ['status' => 'accepted'], ['id' => $inbound['id']]);
        flash('success', 'Interest accepted. You can now message each other.');
        redirect('/messages/' . $targetId);
    }
    if ($outbound && $outbound['status'] === 'sent') {
        flash('success', 'Interest already sent.');
        redirect('/member/' . $targetId);
    }

    $plan = current_plan($uid);
    if (!consume_interest_quota($uid, $plan)) {
        flash('error', 'You have used your 10 free interests for this month. Upgrade for unlimited interests.');
        redirect('/packages');
    }

    if ($outbound && in_array($outbound['status'], ['declined', 'cancelled'], true)) {
        DB::update('interests', ['status' => 'sent'], ['id' => $outbound['id']]);
    } elseif ($inbound && in_array($inbound['status'], ['declined', 'cancelled'], true)) {
        DB::q(
            "UPDATE interests
                SET sender_id = ?, receiver_id = ?, status = 'sent'
              WHERE id = ?",
            [$uid, $targetId, $inbound['id']]
        );
    } else {
        DB::insert('interests', ['sender_id' => $uid, 'receiver_id' => $targetId, 'status' => 'sent']);
    }
    flash('success', 'Interest sent.');
    redirect('/member/' . $targetId);
});

$r->post('/interest/{id}/accept', function ($a) {
    Auth::require();
    $interest = DB::one("SELECT * FROM interests WHERE id = ? AND receiver_id = ? AND status = 'sent'", [$a['id'], Auth::id()]);
    if (!$interest) {
        flash('error', 'That interest is no longer pending.');
        redirect('/interests');
    }
    DB::update('interests', ['status' => 'accepted'], ['id' => $interest['id']]);
    flash('success', 'Interest accepted. You can now message each other.');
    redirect('/messages/' . (int)$interest['sender_id']);
});

$r->post('/interest/{id}/decline', function ($a) {
    Auth::require();
    DB::q("UPDATE interests SET status='declined' WHERE id = ? AND receiver_id = ? AND status = 'sent'", [$a['id'], Auth::id()]);
    redirect('/interests');
});

$r->get('/interests', function () {
    Auth::require();
    $received = DB::all("SELECT i.*, u.name, p.dob, p.city, p.profession, p.verified_tier
                         FROM interests i
                         JOIN users u ON u.id = i.sender_id
                         JOIN profiles p ON p.user_id = u.id
                        WHERE i.receiver_id = ?
                          AND u.role = 'member' AND u.status = 'active'
                     ORDER BY i.created_at DESC", [Auth::id()]);
    $sent     = DB::all("SELECT i.*, u.name, p.dob, p.city, p.profession, p.verified_tier
                         FROM interests i
                         JOIN users u ON u.id = i.receiver_id
                         JOIN profiles p ON p.user_id = u.id
                        WHERE i.sender_id = ?
                          AND u.role = 'member' AND u.status = 'active'
                     ORDER BY i.created_at DESC", [Auth::id()]);
    view('member/interests', compact('received','sent'));
});

// ------------------- SHORTLIST -------------------
$r->post('/shortlist/{id}', function ($a) {
    Auth::require();
    $targetId = (int) $a['id'];
    if ($targetId === Auth::id()) redirect('/browse');
    if (!active_member_profile($targetId)) {
        flash('error', 'This profile is not available.');
        redirect('/browse');
    }
    $exists = DB::val('SELECT id FROM shortlists WHERE user_id = ? AND target_user_id = ?', [Auth::id(), $targetId]);
    if ($exists) {
        DB::q('DELETE FROM shortlists WHERE id = ?', [$exists]);
    } else {
        $plan = current_plan(Auth::id());
        $left = shortlists_left(Auth::id(), $plan);
        if ($left !== null && $left <= 0) {
            flash('error', 'Your free plan can shortlist up to 20 profiles. Upgrade for unlimited shortlists.');
            redirect('/packages');
        }
        DB::insert('shortlists', ['user_id' => Auth::id(), 'target_user_id' => $targetId]);
    }
    redirect('/member/' . $targetId);
});

$r->get('/shortlist', function () {
    Auth::require();
    $rows = DB::all("SELECT u.id, u.name, p.dob, p.city, p.profession, p.about_me, p.height_cm, p.verified_tier, s.spiritual_path
                     FROM shortlists sh
                     JOIN users u ON u.id = sh.target_user_id
                LEFT JOIN profiles p ON p.user_id = u.id
                LEFT JOIN spiritual_details s ON s.user_id = u.id
                    WHERE sh.user_id = ? ORDER BY sh.created_at DESC", [Auth::id()]);
    view('member/shortlist', ['rows' => $rows]);
});

// ------------------- MESSAGES -------------------
$r->get('/messages', function () {
    Auth::require();
    view('messages/index', ['threads' => message_threads(Auth::id())]);
});

$r->get('/messages/{id}', function ($a) {
    Auth::require();
    $otherId = (int) $a['id'];
    $other = active_member_profile($otherId);
    if (!$other) {
        // Sender's account may be paused, blocked or missing a profile row — don't dead-end
        // on a 404 here, which is exactly what made "Accept" feel broken: the connection was
        // saved fine, we just had nowhere to land them.
        flash('error', 'That member is no longer available for chat.');
        redirect('/messages');
    }
    $interest = accepted_interest_between(Auth::id(), $otherId);
    if (!$interest) {
        flash('error', 'Messaging opens after one of you accepts an interest.');
        redirect('/member/' . $otherId);
    }
    $msgs = DB::all('SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC',
        [Auth::id(), $otherId, $otherId, Auth::id()]);
    DB::q('UPDATE messages SET read_at = NOW() WHERE receiver_id = ? AND sender_id = ? AND read_at IS NULL', [Auth::id(), $otherId]);
    $threads = message_threads(Auth::id());
    view('messages/show', ['other' => $other, 'msgs' => $msgs, 'interest' => $interest, 'threads' => $threads]);
});

$r->post('/messages/{id}', function ($a) {
    Auth::require();
    $otherId = (int) $a['id'];
    if (!active_member_profile($otherId)) {
        flash('error', 'That member is no longer available for chat.');
        redirect('/messages');
    }
    if (!can_message_member(Auth::id(), $otherId)) {
        flash('error', 'Messaging opens after one of you accepts an interest.');
        redirect('/member/' . $otherId);
    }
    $body = trim($_POST['body'] ?? '');
    if ($body === '') {
        flash('error', 'Please type something before sending.');
        redirect('/messages/' . $otherId);
    }
    // Match the HTML maxlength but re-enforce here so a scripted client cannot
    // silently drop a novel into the messages table.
    if (mb_strlen($body) > 2000) {
        $body = mb_substr($body, 0, 2000);
    }
    DB::insert('messages', ['sender_id' => Auth::id(), 'receiver_id' => $otherId, 'body' => $body]);
    redirect('/messages/' . $otherId);
});

// ------------------- ACCOUNT SETTINGS -------------------
$r->get('/settings', function () {
    Auth::require();
    view('member/settings');
});

$r->post('/settings', function () {
    Auth::require();
    $u = Auth::user();
    $data = ['name' => trim($_POST['name'] ?? $u['name']), 'phone' => trim($_POST['phone'] ?? $u['phone'])];
    DB::update('users', $data, ['id' => Auth::id()]);
    if (!empty($_POST['new_password']) && password_verify($_POST['current_password'] ?? '', $u['password_hash'])) {
        DB::update('users', ['password_hash' => password_hash($_POST['new_password'], PASSWORD_BCRYPT)], ['id' => Auth::id()]);
        flash('success','Password updated.');
    } else {
        flash('success','Account updated.');
    }
    redirect('/settings');
});
