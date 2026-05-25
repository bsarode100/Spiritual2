<?php /** @var array $packages */ ?>
<section class="section-tight" style="padding: 5rem 0 3rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container text-center">
        <span class="eyebrow">Choose your path</span>
        <h1>Pricing &amp; <em style="color: var(--c-saffron); font-family: var(--f-display);">Packages</em></h1>
        <p style="font-size: 1.1rem; color: var(--c-ink-soft); max-width: 600px; margin: 0 auto;">Three honest plans. Start free. Pay only when you're ready.</p>
    </div>
</section>
<section class="section"><div class="container">
    <div class="pkg-grid">
        <?php foreach ($packages as $p): ?>
            <div class="pkg <?= $p['highlighted'] ? 'featured' : '' ?>">
                <div class="pkg-name"><?= e($p['name']) ?></div>
                <div class="pkg-tag"><?= e($p['tagline']) ?></div>
                <div class="pkg-price">
                    <?php if ($p['price'] > 0): ?>
                        <small>₹</small><?= number_format((float)$p['price'], 0) ?>
                    <?php else: ?>
                        Free
                    <?php endif; ?>
                </div>
                <div class="pkg-duration"><?= (int)$p['duration_days'] ?> days · <?= $p['contacts_limit']==0 ? 'unlimited contacts' : ((int)$p['contacts_limit'] . ' contacts') ?></div>
                <ul class="pkg-features">
                    <?php foreach (explode("\n", $p['features'] ?? '') as $f): $f = trim($f); if (!$f) continue; ?>
                        <li><?= e($f) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="/register" class="btn <?= $p['highlighted'] ? 'btn-gold' : 'btn-ghost' ?> btn-block">
                    <?= $p['price'] > 0 ? 'Choose ' . e($p['name']) : 'Start Free' ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div></section>
