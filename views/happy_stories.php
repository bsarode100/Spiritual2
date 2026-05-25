<?php /** @var array $stories */ ?>
<section class="section-tight" style="padding: 5rem 0 3rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container text-center">
        <span class="eyebrow">Sacred unions</span>
        <h1>Happy <em style="color: var(--c-saffron); font-family: var(--f-display);">Stories</em></h1>
        <p style="font-size: 1.1rem; color: var(--c-ink-soft); max-width: 600px; margin: 0 auto;">Souls who found each other here, and now walk the path together.</p>
    </div>
</section>
<section class="section"><div class="container">
    <?php if (!$stories): ?>
        <div class="admin-card text-center" style="padding: 4rem;"><p>No stories yet.</p></div>
    <?php else: ?>
        <div class="features-grid">
            <?php foreach ($stories as $s): ?>
                <div class="story-card">
                    <p><?= e($s['story']) ?></p>
                    <div class="story-couple">
                        <div class="story-couple-avatar">💞</div>
                        <div>
                            <div class="story-couple-name"><?= e($s['couple_name']) ?></div>
                            <?php if ($s['married_on']): ?><div class="story-couple-date">Married <?= date('M Y', strtotime($s['married_on'])) ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div></section>
