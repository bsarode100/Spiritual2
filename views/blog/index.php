<?php /** @var array $posts */ ?>
<section class="section-tight" style="padding: 5rem 0 3rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container text-center">
        <span class="eyebrow">From our satsang</span>
        <h1>Wisdom &amp; <em style="color: var(--c-saffron); font-family: var(--f-display);">Guidance</em></h1>
        <p style="font-size: 1.1rem; color: var(--c-ink-soft); max-width: 600px; margin: 0 auto;">Reflections on dharma, sadhana, householder life, and partnership.</p>
    </div>
</section>
<section class="section"><div class="container">
    <?php if (!$posts): ?>
        <div class="admin-card text-center" style="padding: 4rem;"><p>No posts yet.</p></div>
    <?php else: ?>
        <div class="blog-grid">
            <?php foreach ($posts as $p): ?>
                <article class="blog-card">
                    <div class="blog-cover">
                        <?php if ($p['cover_image']): ?>
                            <img src="<?= e(upload_url($p['cover_image'])) ?>" alt="">
                        <?php else: ?>
                            <span style="opacity:.7;">ॐ</span>
                        <?php endif; ?>
                    </div>
                    <div class="blog-body">
                        <span class="blog-cat"><?= e($p['category']) ?></span>
                        <h3><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
                        <p class="blog-excerpt"><?= e($p['excerpt']) ?></p>
                        <div class="blog-meta"><span><?= e($p['author_name']) ?></span><span>· <?= date('M j, Y', strtotime($p['published_at'] ?? $p['created_at'])) ?></span></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div></section>
