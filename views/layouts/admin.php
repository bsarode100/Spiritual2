<?php /** @var string $content */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — <?= e(setting('site_name','Spiritual Matrimony')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<div class="admin-shell">
    <aside class="admin-side">
        <a href="/admin" class="brand">
            <div class="brand-icon"><span style="font-family: serif;">ॐ</span></div>
            <span>Admin</span>
        </a>
        <?php $cur = $_SERVER['REQUEST_URI'] ?? ''; ?>
        <ul class="admin-nav">
            <li><a href="/admin"          class="<?= $cur === '/admin' ? 'is-active' : '' ?>">📊 Dashboard</a></li>
            <li><a href="/admin/users"    class="<?= str_contains($cur,'/admin/users') ? 'is-active' : '' ?>">👥 Members</a></li>
            <li><a href="/admin/blog"     class="<?= str_contains($cur,'/admin/blog') ? 'is-active' : '' ?>">📝 Blog</a></li>
            <li><a href="/admin/stories"  class="<?= str_contains($cur,'/admin/stories') ? 'is-active' : '' ?>">💞 Happy Stories</a></li>
            <li><a href="/admin/packages" class="<?= str_contains($cur,'/admin/packages') ? 'is-active' : '' ?>">💎 Packages</a></li>
            <li><a href="/admin/pages"    class="<?= str_contains($cur,'/admin/pages') ? 'is-active' : '' ?>">📄 Pages</a></li>
            <li><a href="/admin/settings" class="<?= str_contains($cur,'/admin/settings') ? 'is-active' : '' ?>">⚙️ Site Settings</a></li>
            <li><a href="/admin/messages" class="<?= str_contains($cur,'/admin/messages') ? 'is-active' : '' ?>">✉️ Contact Messages</a></li>
            <li style="margin-top: 2rem; border-top: 1px solid rgba(255,248,238,.2); padding-top: 1rem;">
                <a href="/admin/profile">👤 My Profile</a></li>
            <li><a href="/" target="_blank">🌐 View Site</a></li>
            <li><a href="/logout">🚪 Sign out</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <?php if ($msg = flash('success')): ?><div class="flash flash-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')):   ?><div class="flash flash-error"><?= e($msg)   ?></div><?php endif; ?>
        <?= $content ?>
    </main>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
