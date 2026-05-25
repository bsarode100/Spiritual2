<?php /** @var array $post, $more */ ?>
<article>
<section class="section-tight" style="padding: 5rem 0 2rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container-sm text-center">
        <span class="eyebrow"><?= e($post['category']) ?></span>
        <h1 style="margin: .3em 0;"><?= e($post['title']) ?></h1>
        <p style="color: var(--c-muted);"><?= e($post['author_name']) ?> · <?= date('F j, Y', strtotime($post['published_at'] ?? $post['created_at'])) ?></p>
    </div>
</section>
<?php if ($post['cover_image']): ?>
<div class="container-sm" style="margin: 0 auto 3rem;">
    <img src="<?= e(upload_url($post['cover_image'])) ?>" alt="" style="width: 100%; border-radius: var(--r-lg); box-shadow: var(--shadow);">
</div>
<?php endif; ?>
<section style="padding-bottom: 4rem;">
    <div class="prose container-sm">
        <?= $post['body'] /* trusted HTML from admin */ ?>
    </div>
</section>
</article>

<?php if ($more): ?>
<section class="section section-soft">
    <div class="container">
        <div class="section-head"><span class="eyebrow">Continue reading</span><h2>More from our satsang</h2></div>
        <div class="blog-grid">
            <?php foreach ($more as $p): ?>
                <article class="blog-card">
                    <div class="blog-cover">
                        <?php if ($p['cover_image']): ?><img src="<?= e(upload_url($p['cover_image'])) ?>" alt=""><?php else: ?><span style="opacity:.7;">ॐ</span><?php endif; ?>
                    </div>
                    <div class="blog-body">
                        <span class="blog-cat"><?= e($p['category']) ?></span>
                        <h3><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
                        <p class="blog-excerpt"><?= e($p['excerpt']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
