<?php /** @var array $packages @var bool $rzp_enabled */
$loggedIn = Auth::check();
?>
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

                <?php if ($p['price'] <= 0): ?>
                    <a href="<?= $loggedIn ? '/dashboard' : '/register' ?>" class="btn btn-ghost btn-block">Start Free</a>
                <?php elseif ($rzp_enabled): ?>
                    <?php if ($loggedIn): ?>
                        <a href="/checkout/<?= (int)$p['id'] ?>" class="btn <?= $p['highlighted'] ? 'btn-gold' : 'btn-primary' ?> btn-block">
                            Pay with Razorpay
                        </a>
                        <a href="/payment-details" class="btn btn-ghost btn-block" style="margin-top: .5rem;">Or pay via UPI / Bank →</a>
                    <?php else: ?>
                        <a href="/register" class="btn <?= $p['highlighted'] ? 'btn-gold' : 'btn-primary' ?> btn-block">
                            Sign up to choose <?= e($p['name']) ?>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= $loggedIn ? '/payment-details' : '/register' ?>" class="btn <?= $p['highlighted'] ? 'btn-gold' : 'btn-ghost' ?> btn-block">
                        Choose <?= e($p['name']) ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top: 3rem;">
        <?php if ($rzp_enabled): ?>
            <p style="color: var(--c-ink-soft);">🔒 Secure payments by <strong>Razorpay</strong> — UPI, cards, netbanking and wallets. Or <a href="/payment-details"><strong>pay manually via UPI / bank transfer →</strong></a></p>
        <?php else: ?>
            <p style="color: var(--c-ink-soft);">Ready to upgrade? See <a href="/payment-details"><strong>payment details &rarr;</strong></a> for our UPI ID and bank account.</p>
        <?php endif; ?>
    </div>
</div></section>
