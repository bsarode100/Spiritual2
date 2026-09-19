<?php /** @var array $stories */ ?>
<section class="section-tight" style="padding: 5.5rem 0 3.5rem; background: radial-gradient(ellipse at 50% 30%, rgba(247, 214, 220, 0.45) 0%, rgba(239, 232, 246, 0.35) 45%, transparent 70%); text-align: center;">
    <div class="container-sm">
        <span class="eyebrow">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            Sacred Unions
        </span>
        <h1>Happy <em style="color: var(--c-saffron); font-family: var(--f-display);">Stories</em></h1>
        <p style="font-size: 1.15rem; color: var(--c-ink-soft); max-width: 580px; margin: 0.8rem auto 0; line-height: 1.65;">
            Souls who recognized each other in this sacred space, and now walk the spiritual path of life hand in hand.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (!$stories): ?>
            <div class="admin-card text-center" style="padding: 4.5rem 2rem;">
                <div style="font-size: 2.8rem; margin-bottom: 1rem;">🕊️</div>
                <h3 style="font-family: var(--f-display); font-size: 1.8rem; color: var(--c-maroon);">No stories published yet</h3>
                <p style="color: var(--c-muted);">Check back soon to read inspiring journeys from newly united couples.</p>
            </div>
        <?php else: ?>
            <div class="features-grid">
                <?php foreach ($stories as $s): ?>
                    <div class="story-card">
                        <p><?= e($s['story']) ?></p>
                        <div class="story-couple">
                            <div class="story-couple-avatar">💞</div>
                            <div>
                                <div class="story-couple-name"><?= e($s['couple_name']) ?></div>
                                <?php if (!empty($s['married_on'])): ?>
                                    <div class="story-couple-date">Married <?= date('M Y', strtotime($s['married_on'])) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
