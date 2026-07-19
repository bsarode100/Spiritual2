<?php /** @var array $packages @var bool $rzp_enabled @var array|null $me */
$loggedIn = Auth::check();
// A stable slug → row map so the comparison table + ordering can rely on
// the canonical five plans regardless of admin-edited display_order.
$bySlug = [];
foreach ($packages as $p) { if (!empty($p['slug'])) $bySlug[$p['slug']] = $p; }
$order = ['free', 'starter', 'divine', 'soul_elite', 'eternal'];
$ordered = [];
foreach ($order as $s) if (isset($bySlug[$s])) $ordered[] = $bySlug[$s];
foreach ($packages as $p) { if (empty($p['slug']) || !in_array($p['slug'], $order, true)) $ordered[] = $p; }

// Helper — renders a ✓ / ✗ / text cell for the comparison table.
$mark = function ($val) {
    if ($val === true  || $val === 1 || $val === '1') return '<span class="cmp-yes">✓</span>';
    if ($val === false || $val === 0 || $val === '0' || $val === null || $val === '') return '<span class="cmp-no">—</span>';
    return '<span class="cmp-text">' . e((string)$val) . '</span>';
};
$currentPlanSlug = $me['plan']['slug'] ?? null;
?>
<section class="pkg-hero">
    <div class="container text-center">
        <span class="eyebrow">Membership</span>
        <h1>Choose your <em style="color: var(--c-saffron); font-family: var(--f-display);">path</em></h1>
        <p class="pkg-hero-lead">Five plans. Real value. Cancel anytime. Verification is separate — pay only for the trust badge you need.</p>
        <?php if ($loggedIn && $me): ?>
            <div class="pkg-hero-meta">
                You are on <strong><?= e($me['plan']['name']) ?></strong>
                <?php if ($me['days_left'] !== null): ?>· <?= (int)$me['days_left'] ?> days remaining<?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section" id="plans"><div class="container">
    <div class="pkg-grid-modern">
        <?php foreach ($ordered as $p):
            $isFree = ((float)$p['price']) <= 0;
            $isCurrent = $currentPlanSlug && $currentPlanSlug === $p['slug'];
            $classes = 'pkg-modern';
            if (!empty($p['ribbon'])) $classes .= ' has-ribbon';
            if (!empty($p['highlighted'])) $classes .= ' is-featured';
            if ($isCurrent) $classes .= ' is-current';
        ?>
            <div class="<?= $classes ?>">
                <?php if (!empty($p['ribbon'])): ?><div class="pkg-ribbon"><?= e($p['ribbon']) ?></div><?php endif; ?>
                <?php if (!empty($p['savings_badge'])): ?><div class="pkg-savings"><?= e($p['savings_badge']) ?></div><?php endif; ?>

                <div class="pkg-name-modern"><?= e($p['name']) ?></div>
                <?php if (!empty($p['tagline'])): ?><div class="pkg-tag-modern"><?= e($p['tagline']) ?></div><?php endif; ?>

                <div class="pkg-price-modern">
                    <?php if ($isFree): ?>
                        <span class="pkg-price-value">₹0</span>
                        <span class="pkg-price-note">Lifetime</span>
                    <?php else: ?>
                        <span class="pkg-price-value"><small>₹</small><?= number_format((float)$p['price'], 0) ?></span>
                        <span class="pkg-price-note">
                            <?= (int)$p['duration_months'] ?> month<?= ((int)$p['duration_months']) === 1 ? '' : 's' ?>
                            <?php if (!empty($p['monthly_display']) && (int)$p['duration_months'] > 1): ?>
                                · ₹<?= number_format((float)$p['monthly_display'], 0) ?>/mo
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>

                <ul class="pkg-features-modern">
                    <?php foreach (explode("\n", (string)($p['features'] ?? '')) as $f):
                        $f = trim($f); if (!$f) continue; ?>
                        <li><?= e($f) ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="pkg-cta-modern">
                    <?php if ($isCurrent): ?>
                        <span class="btn btn-ghost btn-block" style="cursor:default;">Your current plan</span>
                    <?php elseif ($isFree): ?>
                        <a href="<?= $loggedIn ? '/dashboard' : '/register' ?>" class="btn btn-ghost btn-block">Start Free</a>
                    <?php elseif ($rzp_enabled): ?>
                        <?php if ($loggedIn): ?>
                            <a href="/checkout/<?= (int)$p['id'] ?>" class="btn <?= !empty($p['highlighted']) ? 'btn-gold' : 'btn-primary' ?> btn-block">
                                Get <?= e($p['name']) ?>
                            </a>
                            <a href="/payment-details" class="btn btn-ghost btn-block btn-quiet">Or pay via UPI / Bank →</a>
                        <?php else: ?>
                            <a href="/register" class="btn <?= !empty($p['highlighted']) ? 'btn-gold' : 'btn-primary' ?> btn-block">
                                Sign up to choose <?= e($p['name']) ?>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= $loggedIn ? '/payment-details' : '/register' ?>" class="btn <?= !empty($p['highlighted']) ? 'btn-gold' : 'btn-primary' ?> btn-block">
                            Choose <?= e($p['name']) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div></section>

<!-- ================= COMPARISON TABLE ================= -->
<section class="section" style="padding-top: 0;">
<div class="container">
    <div class="text-center mb-3">
        <span class="eyebrow">Features</span>
        <h2>Compare all plans</h2>
    </div>
    <div class="pkg-cmp-wrap">
    <table class="pkg-cmp">
        <thead>
            <tr>
                <th class="cmp-row-label">&nbsp;</th>
                <?php foreach ($ordered as $p): ?>
                    <th class="<?= !empty($p['highlighted']) ? 'is-highlight' : '' ?>">
                        <?= e($p['name']) ?>
                        <?php if (!empty($p['ribbon'])): ?><div class="cmp-hd-ribbon"><?= e($p['ribbon']) ?></div><?php endif; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $rows = [
                'Duration' => function($p) { $m = (int)$p['duration_months']; return $m === 0 ? 'Lifetime' : $m . ' month' . ($m === 1 ? '' : 's'); },
                'Send Interests' => function($p) { return (int)$p['interests_per_month'] === 0 ? 'Unlimited' : $p['interests_per_month'] . '/month'; },
                'View Profile Photos' => function($p) { return (int)$p['unlimited_photos'] === 1 ? 'Unlimited' : 'Limited'; },
                'View Contact Details' => function($p) { $c = (int)$p['contacts_limit']; return ((float)$p['price']) === 0.0 ? false : ($c === 0 ? 'Unlimited' : (string)$c); },
                'Chat with Accepted Matches' => function($p) { return true; },
                'Unlimited Search' => function($p) { return (int)$p['unlimited_search'] === 1; },
                'Basic Search Filters' => function($p) { return true; },
                'Advanced Search Filters' => function($p) { return (int)$p['advanced_search'] === 1; },
                'See Who Viewed Profile' => function($p) { return (int)$p['see_who_viewed'] === 1; },
                'See Who Shortlisted You' => function($p) { return (int)$p['see_who_shortlisted'] === 1; },
                'Shortlist Profiles' => function($p) { $s = (int)$p['shortlist_limit']; return $s === 0 ? 'Unlimited' : (string)$s; },
                'Priority in Search Results' => function($p) {
                    return ['Lowest','Standard','Medium','High','Highest'][max(0, min(4, ((int)$p['priority_rank']) - 1))];
                },
                'Featured Profile' => function($p) {
                    if ((int)$p['always_featured'] === 1) return 'Always';
                    return (int)$p['featured_days'] > 0 ? ((int)$p['featured_days'] . ' days') : false;
                },
                'Profile Boost' => function($p) { return (int)$p['boosts_per_month'] > 0 ? ($p['boosts_per_month'] . '/month') : false; },
                'Customer Support' => function($p) { return $p['support_tier'] ?: 'Email'; },
                'Match Suggestions' => function($p) { return $p['match_suggestions'] ?: 'Basic'; },
                'Premium Membership Badge' => function($p) { return (int)$p['premium_badge'] === 1; },
            ];
            foreach ($rows as $label => $fn): ?>
                <tr>
                    <td class="cmp-row-label"><?= e($label) ?></td>
                    <?php foreach ($ordered as $p): ?>
                        <td class="<?= !empty($p['highlighted']) ? 'is-highlight' : '' ?>"><?= $mark($fn($p)) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
</section>

<section class="section-tight"><div class="container text-center">
    <p style="color: var(--c-ink-soft); max-width: 720px; margin: 0 auto 1rem;">
        Looking for a quick tune-up instead of a full plan?
        <a href="/addons">See add-ons →</a> or <a href="/verification">verify your profile →</a>.
    </p>
    <?php if ($rzp_enabled): ?>
        <p style="color: var(--c-muted);">🔒 Secure payments by <strong>Razorpay</strong> — UPI, cards, netbanking and wallets. Or <a href="/payment-details"><strong>pay manually via UPI / bank transfer →</strong></a></p>
    <?php else: ?>
        <p style="color: var(--c-muted);">Ready to upgrade? See <a href="/payment-details"><strong>payment details →</strong></a> for UPI ID and bank account.</p>
    <?php endif; ?>
</div></section>

<!-- Sticky mobile-only Upgrade CTA -->
<div class="pkg-sticky-mobile">
    <a href="#plans" class="btn btn-gold btn-block">Upgrade now</a>
</div>
