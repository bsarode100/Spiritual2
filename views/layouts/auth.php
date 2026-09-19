<?php /** @var string $content */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(setting('site_name', 'SpiritualShaadi')) ?> — Welcome</title>
<meta name="description" content="<?= e(setting('site_tagline', 'Find a Perfect Spiritual Life Partner')) ?>">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="192x192" href="<?= asset('images/logo.png') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= asset('images/logo.png') ?>">
<link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e(setting('site_name', 'SpiritualShaadi')) ?> — Welcome">
<meta property="og:description" content="<?= e(setting('site_tagline', 'Find a Perfect Spiritual Life Partner')) ?>">
<meta property="og:image" content="<?= e(setting('app_url', rtrim($GLOBALS['CFG']['app']['url'] ?? 'https://spiritualshaadi.com', '/'))) ?>/assets/images/logo.png">
<meta property="og:site_name" content="<?= e(setting('site_name', 'SpiritualShaadi')) ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,500;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<div class="auth-shell">
    <aside class="auth-art">
        <a href="/" class="brand" style="color: var(--c-cream);">
            <div class="brand-icon">
                <img src="<?= asset('images/logo.png') ?>" alt="SpiritualShaadi Logo" style="height: 36px; width: auto;">
            </div>
            <span><?= e(setting('site_name', 'SpiritualShaadi')) ?></span>
        </a>
        <div>
            <p class="script" style="color: var(--c-saffron); margin-bottom: 0;">two souls,</p>
            <h2 style="font-style: italic;">one path</h2>
            <p class="quote">A sacred space for sincere seekers — devotees, sadhakas, yogis, and dharmics — looking for a life-companion aligned with the spiritual journey.</p>
        </div>
        <div style="opacity: .7; font-size: .9rem; position: relative; z-index: 2;">
            🪷 25,000+ sincere seekers · 1,200+ marriages
        </div>
    </aside>

    <div class="auth-form-shell">
        <div class="auth-form-shell-inner">
            <?php if ($msg = flash('success')): ?><div class="flash flash-success"><?= e($msg) ?></div><?php endif; ?>
            <?php if ($msg = flash('error')):   ?><div class="flash flash-error"><?= e($msg)   ?></div><?php endif; ?>
            <?= $content ?>
        </div>
    </div>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
