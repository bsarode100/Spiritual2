<?php /** @var array $addons; @var bool $rzp_enabled */ $loggedIn = Auth::check(); ?>
<section class="pkg-hero">
    <div class="container text-center">
        <span class="eyebrow">Add-ons</span>
        <h1>Boost <em style="color: var(--c-saffron); font-family: var(--f-display);">visibility</em> when you need it</h1>
        <p class="pkg-hero-lead">Optional one-time boosts and packs. Layer them on top of any plan — even Free.</p>
    </div>
</section>

<section class="section" style="padding-top: 0;"><div class="container">
    <?php if (!$addons): ?>
        <div class="admin-card text-center"><p>No add-ons are active right now.</p></div>
    <?php else: ?>
        <div class="pkg-grid-modern">
            <?php foreach ($addons as $a): ?>
                <div class="pkg-modern">
                    <div class="pkg-name-modern"><?= e($a['name']) ?></div>
                    <?php if (!empty($a['description'])): ?><div class="pkg-tag-modern"><?= e($a['description']) ?></div><?php endif; ?>

                    <div class="pkg-price-modern">
                        <span class="pkg-price-value"><small>₹</small><?= number_format((float)$a['price'], 0) ?></span>
                        <span class="pkg-price-note">
                            <?php if ((int)$a['duration_days'] > 0): ?>
                                <?= (int)$a['duration_days'] ?> day<?= (int)$a['duration_days'] === 1 ? '' : 's' ?>
                            <?php elseif ((int)$a['quantity'] > 0): ?>
                                <?= (int)$a['quantity'] ?> credits
                            <?php else: ?>
                                One-time
                            <?php endif; ?>
                        </span>
                    </div>

                    <ul class="pkg-features-modern">
                        <li><?= e(ucfirst(str_replace('_', ' ', $a['kind']))) ?></li>
                        <?php if ((int)$a['duration_days'] > 0): ?>
                            <li>Active for <?= (int)$a['duration_days'] ?> days after purchase</li>
                        <?php endif; ?>
                        <?php if ((int)$a['quantity'] > 0): ?>
                            <li><?= (int)$a['quantity'] ?> extra credits added to your account</li>
                        <?php endif; ?>
                        <li>Stacks on top of your plan</li>
                    </ul>

                    <div class="pkg-cta-modern">
                        <?php if ($loggedIn && $rzp_enabled): ?>
                            <a href="/checkout/addon/<?= (int)$a['id'] ?>" class="btn btn-primary btn-block">Buy Add-on</a>
                            <a href="/payment-details" class="btn btn-ghost btn-block btn-quiet">Or pay via UPI / Bank →</a>
                        <?php elseif ($loggedIn): ?>
                            <a href="/payment-details" class="btn btn-primary btn-block">Pay Manually</a>
                        <?php else: ?>
                            <a href="/register" class="btn btn-primary btn-block">Sign Up to Buy</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-3">
        <p style="color: var(--c-muted);">Looking for a plan instead? <a href="/packages"><strong>See all membership plans →</strong></a></p>
    </div>
</div></section>
