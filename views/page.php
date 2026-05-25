<?php /** @var array $page */ ?>
<section class="section-tight" style="padding: 5rem 0 2rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container-sm text-center">
        <span class="eyebrow"><?= e($page['slug']) ?></span>
        <h1><?= e($page['title']) ?></h1>
    </div>
</section>
<section class="section">
    <div class="prose container-sm"><?= $page['body'] /* trusted HTML from admin */ ?></div>
</section>
