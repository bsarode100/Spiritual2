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
    view('admin/user_show', compact('u','p','sp'), 'admin');
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
    DB::q('DELETE FROM packages WHERE id = ?', [$a['id']]);
    flash('success','Package deleted.');
    redirect('/admin/packages');
}));

function admin_pkg_data(): array {
    return [
        'name'           => trim($_POST['name'] ?? ''),
        'tagline'        => $_POST['tagline'] ?? null,
        'price'          => (float)($_POST['price'] ?? 0),
        'currency'       => $_POST['currency'] ?? 'INR',
        'duration_days'  => (int)($_POST['duration_days'] ?? 90),
        'contacts_limit' => (int)($_POST['contacts_limit'] ?? 0),
        'features'       => $_POST['features'] ?? '',
        'highlighted'    => isset($_POST['highlighted']) ? 1 : 0,
        'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        'display_order'  => (int)($_POST['display_order'] ?? 0),
    ];
}

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

$r->get('/admin/pages/{id}/edit', $admin(function ($a) {
    $page = DB::one('SELECT * FROM pages WHERE id = ?', [$a['id']]);
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
