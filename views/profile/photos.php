<?php /** @var array $photos */ ?>
<section class="section-tight"><div class="container">
    <div class="flex-between mb-4">
        <div><span class="eyebrow">Your gallery</span><h1 style="margin:0;">Photos</h1></div>
        <a href="/dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <div class="admin-card mb-3">
        <form method="post" action="/profile/photos" enctype="multipart/form-data" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <?= csrf_field() ?>
            <div class="field" style="flex:1; margin-bottom:0;">
                <label>Add a new photo</label>
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>
                <span class="field-help">JPG, PNG, or WEBP up to 4MB. Your first photo becomes your primary automatically.</span>
            </div>
            <button class="btn btn-primary">Upload</button>
        </form>
    </div>

    <?php if (!$photos): ?>
        <div class="admin-card text-center" style="padding: 4rem 2rem;">
            <p style="color: var(--c-muted);">No photos yet. Add at least one so other seekers can see who you are.</p>
        </div>
    <?php else: ?>
        <div class="photo-grid">
            <?php foreach ($photos as $p): ?>
                <div class="photo-tile <?= $p['is_primary'] ? 'primary' : '' ?>">
                    <img src="<?= e(upload_url($p['path'])) ?>" alt="">
                    <div class="photo-actions">
                        <?php if (!$p['is_primary']): ?>
                            <form method="post" action="/profile/photos/<?= (int)$p['id'] ?>/primary">
                                <?= csrf_field() ?>
                                <button>Set primary</button>
                            </form>
                        <?php else: ?>
                            <span class="pill gold" style="background: var(--c-saffron); color: white;">Primary</span>
                        <?php endif; ?>
                        <form method="post" action="/profile/photos/<?= (int)$p['id'] ?>/delete" onsubmit="return confirm('Delete this photo?')">
                            <?= csrf_field() ?>
                            <button>Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div></section>
