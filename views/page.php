<?php /** @var array $page */ ?>
<section class="section-tight" style="padding: 5rem 0 3rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container text-center">
        <span class="eyebrow"><?= e(str_replace('-', ' ', $page['slug'])) ?></span>
        <h1><?= e($page['title']) ?></h1>
    </div>
</section>
<section class="section"><div class="container">
    <div class="prose"><?= format_page_body($page['body']) ?></div>
</div></section>
