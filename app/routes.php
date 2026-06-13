<?php
// Public + member routes (admin routes live in admin_routes.php).
/** @var Router $r */

// ------------------- HOME -------------------
$r->get('/', function () {
    $featured = DB::all("SELECT u.*, p.gender, p.dob, p.city, p.state, p.country, p.profession, p.about_me, p.education, p.height_cm, s.spiritual_path
                          FROM users u
                          JOIN profiles p ON p.user_id = u.id
                     LEFT JOIN spiritual_details s ON s.user_id = u.id
                         WHERE u.role = 'member' AND u.status = 'active' AND p.profile_complete = 1
                      ORDER BY u.created_at DESC LIMIT 6");
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

$r->get('/about',   function () { $page = DB::one("SELECT * FROM pages WHERE slug='about'");   view('page', ['page' => $page]); });
$r->get('/privacy', function () { $page = DB::one("SELECT * FROM pages WHERE slug='privacy'"); view('page', ['page' => $page]); });
$r->get('/terms',   function () { $page = DB::one("SELECT * FROM pages WHERE slug='terms'");   view('page', ['page' => $page]); });

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
    view('packages/index', ['packages' => $packages]);
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
    if (DB::val('SELECT 1 FROM users WHERE email = ?', [$email])) {
        flash('error', 'An account with this email already exists.');
        redirect('/register');
    }
    $uid = DB::insert('users', [
        'name'          => $name,
        'email'         => $email,
        'phone'         => $phone,
        'password_hash' => password_hash($pass, PASSWORD_BCRYPT),
        'role'          => 'member',
        'status'        => 'active',
    ]);
    DB::insert('profiles', [
        'user_id'         => $uid,
        'gender'          => $gender,
        'dob'             => $dob,
        'profile_complete'=> 0,
    ]);
    Auth::login($uid);
    flash('success', 'Welcome! Complete your profile so others can find you.');
    redirect('/profile/edit');
});

$r->post('/logout', function () {
    Auth::logout();
    redirect('/');
});
// also allow GET for convenience
$r->get('/logout', function () { Auth::logout(); redirect('/'); });

// ------------------- MEMBER DASHBOARD -------------------
$r->get('/dashboard', function () {
    Auth::require();
    $uid = Auth::id();
    $stats = [
        'interests_received' => DB::val('SELECT COUNT(*) FROM interests WHERE receiver_id = ? AND status = "sent"', [$uid]),
        'interests_accepted' => DB::val('SELECT COUNT(*) FROM interests WHERE (sender_id = ? OR receiver_id = ?) AND status = "accepted"', [$uid,$uid]),
        'shortlisted'        => DB::val('SELECT COUNT(*) FROM shortlists WHERE user_id = ?', [$uid]),
        'profile_views'      => DB::val('SELECT views FROM profiles WHERE user_id = ?', [$uid]),
    ];
    $matches = DB::all("SELECT u.*, p.gender, p.dob, p.city, p.profession, p.about_me, p.height_cm, s.spiritual_path
                        FROM users u
                        JOIN profiles p ON p.user_id = u.id
                   LEFT JOIN spiritual_details s ON s.user_id = u.id
                       WHERE u.id != ? AND u.status = 'active' AND p.profile_complete = 1
                         AND p.gender != (SELECT gender FROM profiles WHERE user_id = ?)
                    ORDER BY u.created_at DESC LIMIT 6", [$uid,$uid]);
    $recent_interests = DB::all("SELECT i.*, u.name, u.id AS uid FROM interests i JOIN users u ON u.id = i.sender_id
                                 WHERE i.receiver_id = ? ORDER BY i.created_at DESC LIMIT 5", [$uid]);
    view('member/dashboard', compact('stats','matches','recent_interests'));
});

// ------------------- PROFILE EDIT -------------------
$r->get('/profile/edit', function () {
    Auth::require();
    $uid = Auth::id();
    $profile = DB::one('SELECT * FROM profiles WHERE user_id = ?', [$uid]);
    $spiritual = DB::one('SELECT * FROM spiritual_details WHERE user_id = ?', [$uid]);
    $horoscope = DB::one('SELECT * FROM horoscopes WHERE user_id = ?', [$uid]);
    view('profile/edit', compact('profile','spiritual','horoscope'));
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
    // mark complete if required fields exist
    $required = ['gender','dob','city','about_me'];
    $complete = 1;
    foreach ($required as $rq) { if (empty($data[$rq])) $complete = 0; }
    $data['profile_complete'] = $complete;
    DB::update('profiles', $data, ['user_id' => $uid]);

    // also update user name
    if (!empty($_POST['name'])) {
        DB::update('users', ['name' => trim($_POST['name'])], ['id' => $uid]);
    }

    flash('success', 'Profile saved.');
    redirect('/profile/edit');
});

// ------------------- SPIRITUAL DETAILS -------------------
$r->post('/profile/spiritual', function () {
    Auth::require();
    $uid = Auth::id();
    $fields = ['spiritual_path','guru','ishta_devata','daily_sadhana','favorite_scripture','fasting_practice','pilgrimage_done','mantra'];
    $data = ['user_id' => $uid];
    foreach ($fields as $k) $data[$k] = $_POST[$k] ?? null;

    $exists = DB::val('SELECT id FROM spiritual_details WHERE user_id = ?', [$uid]);
    if ($exists) {
        unset($data['user_id']);
        DB::update('spiritual_details', $data, ['user_id' => $uid]);
    } else {
        DB::insert('spiritual_details', $data);
    }
    flash('success', 'Spiritual details saved.');
    redirect('/profile/edit#spiritual');
});

// ------------------- PHOTOS -------------------
$r->get('/profile/photos', function () {
    Auth::require();
    $photos = DB::all('SELECT * FROM photos WHERE user_id = ? ORDER BY is_primary DESC, id', [Auth::id()]);
    view('profile/photos', ['photos' => $photos]);
});

$r->post('/profile/photos', function () {
    Auth::require();
    $cfg = $GLOBALS['CFG']['uploads'];
    if (empty($_FILES['photo']['name'])) { flash('error','Choose a photo.'); redirect('/profile/photos'); }
    $f = $_FILES['photo'];
    if ($f['error'] !== UPLOAD_ERR_OK) { flash('error','Upload failed.'); redirect('/profile/photos'); }
    if ($f['size'] > $cfg['max_bytes']) { flash('error','Photo too large (max 4MB).'); redirect('/profile/photos'); }
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $cfg['allowed'])) { flash('error','JPG / PNG / WEBP only.'); redirect('/profile/photos'); }
    if (!is_dir($cfg['avatar_dir'])) mkdir($cfg['avatar_dir'], 0775, true);
    $name = Auth::id() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    move_uploaded_file($f['tmp_name'], $cfg['avatar_dir'] . '/' . $name);
    $isFirst = (int) DB::val('SELECT COUNT(*) FROM photos WHERE user_id = ?', [Auth::id()]) === 0 ? 1 : 0;
    DB::insert('photos', ['user_id' => Auth::id(), 'path' => 'avatars/' . $name, 'is_primary' => $isFirst]);
    flash('success','Photo uploaded.');
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
    $row = DB::one('SELECT * FROM photos WHERE id = ? AND user_id = ?', [$a['id'], Auth::id()]);
    if ($row) {
        @unlink(__DIR__ . '/../public/uploads/' . $row['path']);
        DB::q('DELETE FROM photos WHERE id = ?', [$row['id']]);
    }
    redirect('/profile/photos');
});

// ------------------- BROWSE / SEARCH -------------------
$r->get('/browse', function () {
    Auth::require();
    $me  = DB::one('SELECT * FROM profiles WHERE user_id = ?', [Auth::id()]);
    $opp = $me && $me['gender'] === 'male' ? 'female' : 'male';
    $where  = ["u.status = 'active'", "u.role = 'member'", 'u.id != :me', 'p.profile_complete = 1', 'p.gender = :g'];
    $params = ['me' => Auth::id(), 'g' => $opp];

    if (!empty($_GET['city']))     { $where[] = 'p.city LIKE :city';   $params['city']   = '%' . $_GET['city'] . '%'; }
    if (!empty($_GET['religion'])) { $where[] = 'p.religion = :rel';   $params['rel']    = $_GET['religion']; }
    if (!empty($_GET['diet']))     { $where[] = 'p.diet = :diet';      $params['diet']   = $_GET['diet']; }
    if (!empty($_GET['path']))     { $where[] = 's.spiritual_path LIKE :sp'; $params['sp'] = '%' . $_GET['path'] . '%'; }
    if (!empty($_GET['min_age']))  { $where[] = 'TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= :min'; $params['min'] = (int)$_GET['min_age']; }
    if (!empty($_GET['max_age']))  { $where[] = 'TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) <= :max'; $params['max'] = (int)$_GET['max_age']; }

    $page = max(1, (int)($_GET['page'] ?? 1));
    $per  = 9;
    $total = (int) DB::val("SELECT COUNT(*) FROM users u JOIN profiles p ON p.user_id = u.id
                            LEFT JOIN spiritual_details s ON s.user_id = u.id
                            WHERE " . implode(' AND ', $where), $params);
    $pg = paginate($total, $per, $page);

    $rows = DB::all("SELECT u.id, u.name, p.dob, p.gender, p.city, p.state, p.country, p.height_cm,
                            p.profession, p.education, p.about_me, p.religion, p.community, s.spiritual_path, s.guru
                       FROM users u
                       JOIN profiles p ON p.user_id = u.id
                  LEFT JOIN spiritual_details s ON s.user_id = u.id
                      WHERE " . implode(' AND ', $where) . "
                   ORDER BY u.created_at DESC
                      LIMIT {$pg['limit']} OFFSET {$pg['offset']}", $params);

    view('browse/index', ['rows' => $rows, 'page' => $pg, 'total' => $total]);
});

// ------------------- MEMBER PROFILE VIEW -------------------
$r->get('/member/{id}', function ($a) {
    Auth::require();
    // p.* first, then u.* — so duplicate keys (id, created_at, updated_at) resolve to the users row.
    $u = DB::one("SELECT p.*, u.id, u.name, u.email, u.phone, u.role, u.status, u.created_at, u.updated_at
                    FROM users u JOIN profiles p ON p.user_id = u.id WHERE u.id = ?", [$a['id']]);
    if (!$u || $u['role'] !== 'member') { http_response_code(404); view('errors/404'); return; }
    $sp = DB::one('SELECT * FROM spiritual_details WHERE user_id = ?', [$a['id']]);
    $photos = DB::all('SELECT * FROM photos WHERE user_id = ? ORDER BY is_primary DESC', [$a['id']]);
    DB::q('UPDATE profiles SET views = views + 1 WHERE user_id = ?', [$a['id']]);
    $interest = DB::one('SELECT * FROM interests WHERE sender_id = ? AND receiver_id = ?', [Auth::id(), $a['id']]);
    $shortlisted = (bool) DB::val('SELECT 1 FROM shortlists WHERE user_id = ? AND target_user_id = ?', [Auth::id(), $a['id']]);
    view('member/show', compact('u','sp','photos','interest','shortlisted'));
});

// ------------------- INTERESTS -------------------
$r->post('/interest/send/{id}', function ($a) {
    Auth::require();
    if ($a['id'] == Auth::id()) redirect('/browse');
    $exists = DB::val('SELECT id FROM interests WHERE sender_id = ? AND receiver_id = ?', [Auth::id(), $a['id']]);
    if (!$exists) {
        DB::insert('interests', ['sender_id' => Auth::id(), 'receiver_id' => $a['id'], 'status' => 'sent']);
    }
    flash('success', 'Interest sent.');
    redirect('/member/' . $a['id']);
});

$r->post('/interest/{id}/accept', function ($a) {
    Auth::require();
    DB::q("UPDATE interests SET status='accepted' WHERE id = ? AND receiver_id = ?", [$a['id'], Auth::id()]);
    flash('success', 'Interest accepted — you can now message each other.');
    redirect('/interests');
});

$r->post('/interest/{id}/decline', function ($a) {
    Auth::require();
    DB::q("UPDATE interests SET status='declined' WHERE id = ? AND receiver_id = ?", [$a['id'], Auth::id()]);
    redirect('/interests');
});

$r->get('/interests', function () {
    Auth::require();
    $received = DB::all("SELECT i.*, u.name, p.dob, p.city, p.profession
                         FROM interests i JOIN users u ON u.id = i.sender_id
                    LEFT JOIN profiles p ON p.user_id = u.id
                        WHERE i.receiver_id = ? ORDER BY i.created_at DESC", [Auth::id()]);
    $sent     = DB::all("SELECT i.*, u.name, p.dob, p.city, p.profession
                         FROM interests i JOIN users u ON u.id = i.receiver_id
                    LEFT JOIN profiles p ON p.user_id = u.id
                        WHERE i.sender_id = ? ORDER BY i.created_at DESC", [Auth::id()]);
    view('member/interests', compact('received','sent'));
});

// ------------------- SHORTLIST -------------------
$r->post('/shortlist/{id}', function ($a) {
    Auth::require();
    if ($a['id'] == Auth::id()) redirect('/browse');
    $exists = DB::val('SELECT id FROM shortlists WHERE user_id = ? AND target_user_id = ?', [Auth::id(), $a['id']]);
    if ($exists) {
        DB::q('DELETE FROM shortlists WHERE id = ?', [$exists]);
    } else {
        DB::insert('shortlists', ['user_id' => Auth::id(), 'target_user_id' => $a['id']]);
    }
    redirect('/member/' . $a['id']);
});

$r->get('/shortlist', function () {
    Auth::require();
    $rows = DB::all("SELECT u.id, u.name, p.dob, p.city, p.profession, p.about_me, p.height_cm, s.spiritual_path
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
    $uid = Auth::id();
    $threads = DB::all("SELECT DISTINCT
                          CASE WHEN m.sender_id = :u THEN m.receiver_id ELSE m.sender_id END AS other_id,
                          u.name AS other_name,
                          (SELECT body FROM messages mx
                            WHERE (mx.sender_id = :u AND mx.receiver_id = u.id)
                               OR (mx.sender_id = u.id AND mx.receiver_id = :u)
                            ORDER BY mx.created_at DESC LIMIT 1) AS last_msg,
                          (SELECT created_at FROM messages mx
                            WHERE (mx.sender_id = :u AND mx.receiver_id = u.id)
                               OR (mx.sender_id = u.id AND mx.receiver_id = :u)
                            ORDER BY mx.created_at DESC LIMIT 1) AS last_at
                        FROM messages m
                        JOIN users u ON u.id = (CASE WHEN m.sender_id = :u THEN m.receiver_id ELSE m.sender_id END)
                        WHERE m.sender_id = :u OR m.receiver_id = :u
                        ORDER BY last_at DESC", ['u' => $uid]);
    view('messages/index', ['threads' => $threads]);
});

$r->get('/messages/{id}', function ($a) {
    Auth::require();
    $other = DB::one('SELECT * FROM users WHERE id = ?', [$a['id']]);
    if (!$other) { http_response_code(404); view('errors/404'); return; }
    $msgs = DB::all('SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC',
        [Auth::id(), $a['id'], $a['id'], Auth::id()]);
    DB::q('UPDATE messages SET read_at = NOW() WHERE receiver_id = ? AND sender_id = ? AND read_at IS NULL', [Auth::id(), $a['id']]);
    view('messages/show', ['other' => $other, 'msgs' => $msgs]);
});

$r->post('/messages/{id}', function ($a) {
    Auth::require();
    $body = trim($_POST['body'] ?? '');
    if ($body !== '') {
        DB::insert('messages', ['sender_id' => Auth::id(), 'receiver_id' => $a['id'], 'body' => $body]);
    }
    redirect('/messages/' . $a['id']);
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
