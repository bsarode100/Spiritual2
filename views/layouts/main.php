<?php /** @var string $content */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(setting('site_name', 'SpiritualShaadi')) ?><?= isset($title) ? ' — ' . e($title) : '' ?></title>
<meta name="description" content="<?= e(setting('site_tagline', 'Find a Perfect Spiritual Life Partner')) ?>">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="192x192" href="<?= asset('images/logo.png') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= asset('images/logo.png') ?>">
<link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">

<!-- Open Graph (Facebook, WhatsApp, etc.) -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e(setting('site_name', 'SpiritualShaadi')) ?><?= isset($title) ? ' — ' . e($title) : '' ?>">
<meta property="og:description" content="<?= e(setting('site_tagline', 'Find a Perfect Spiritual Life Partner')) ?>">
<meta property="og:image" content="<?= e(setting('app_url', rtrim($GLOBALS['CFG']['app']['url'] ?? 'https://spiritualshaadi.com', '/'))) ?>/assets/images/logo.png">
<meta property="og:url" content="<?= e(setting('app_url', rtrim($GLOBALS['CFG']['app']['url'] ?? 'https://spiritualshaadi.com', '/'))) ?>">
<meta property="og:site_name" content="<?= e(setting('site_name', 'SpiritualShaadi')) ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?= e(setting('site_name', 'SpiritualShaadi')) ?><?= isset($title) ? ' — ' . e($title) : '' ?>">
<meta name="twitter:description" content="<?= e(setting('site_tagline', 'Find a Perfect Spiritual Life Partner')) ?>">
<meta name="twitter:image" content="<?= e(setting('app_url', rtrim($GLOBALS['CFG']['app']['url'] ?? 'https://spiritualshaadi.com', '/'))) ?>/assets/images/logo.png">

<!-- Structured Data for Google Search (Logo + Site Name) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?= e(setting('site_name', 'SpiritualShaadi')) ?>",
  "url": "<?= e(setting('app_url', rtrim($GLOBALS['CFG']['app']['url'] ?? 'https://spiritualshaadi.com', '/'))) ?>",
  "logo": "<?= e(setting('app_url', rtrim($GLOBALS['CFG']['app']['url'] ?? 'https://spiritualshaadi.com', '/'))) ?>/assets/images/logo.png",
  "description": "<?= e(setting('site_tagline', 'Find a Perfect Spiritual Life Partner')) ?>"
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "<?= e(setting('site_name', 'SpiritualShaadi')) ?>",
  "url": "<?= e(setting('app_url', rtrim($GLOBALS['CFG']['app']['url'] ?? 'https://spiritualshaadi.com', '/'))) ?>"
}
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,500;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<?php
$_isChatPage = preg_match('#^/messages/\d+#', parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
?>
<body class="<?= $_isChatPage ? 'is-chat-page' : '' ?>">
<?php include __DIR__ . '/../partials/nav.php'; ?>

<main>
    <?php if ($msg = flash('success')): ?>
        <div class="container mt-3"><div class="flash flash-success"><?= e($msg) ?></div></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="container mt-3"><div class="flash flash-error"><?= e($msg) ?></div></div>
    <?php endif; ?>

    <?= $content ?>
</main>

<?php include __DIR__ . '/../partials/bottom_nav.php'; ?>

<?php
$_footerUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$_isHome = ($_footerUri === '/' || $_footerUri === '');
if ($_isHome): ?>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
<?php else: ?>
    <div class="footer-hide-mobile">
        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
<?php endif; ?>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
