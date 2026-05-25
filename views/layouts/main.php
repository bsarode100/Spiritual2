<?php /** @var string $content */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(setting('site_name', 'Spiritual Matrimony')) ?><?= isset($title) ? ' — ' . e($title) : '' ?></title>
<meta name="description" content="<?= e(setting('site_tagline', 'Where two souls meet on the same path.')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,500;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%237B1F1F'/><text x='50' y='68' text-anchor='middle' font-family='serif' font-size='52' fill='%23D4A017'>ॐ</text></svg>">
</head>
<body>
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

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
