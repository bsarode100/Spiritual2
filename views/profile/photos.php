<?php
/** @var array $photos */
/** @var array $missing */
/** @var int|null $photoLimit */
/** @var array $plan */
$missing = $missing ?? [];
$count = count($photos);
$photoLimit = $photoLimit ?? PROFILE_PHOTO_MAX;
$atMax = $photoLimit !== null && $count >= $photoLimit;
$belowMin = $count < PROFILE_PHOTO_MIN;
$needed = max(0, PROFILE_PHOTO_MIN - $count);
// Missing bio fields (everything except the photo requirement itself) — we
// surface these here too, so the user always sees the full "what's blocking
// Express Interest" picture even when they're only on the gallery page.
$bioMissing = array_diff_key($missing, ['photos' => true]);
?>
<section class="section-tight"><div class="container">
    <div class="flex-between mb-4">
        <div><span class="eyebrow">Your gallery</span><h1 style="margin:0;">Photos</h1></div>
        <a href="/dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <?php if ($bioMissing): ?>
        <div class="profile-missing-banner mb-3">
            <strong>Your bio-data also needs a few things.</strong> Photos alone aren't enough to unlock Express Interest — please also complete:
            <ul>
                <?php foreach ($bioMissing as $label): ?>
                    <li><?= e($label) ?></li>
                <?php endforeach; ?>
            </ul>
            <p style="margin: .5rem 0 0;"><a href="/profile/edit">Finish your bio-data →</a></p>
        </div>
    <?php endif; ?>

    <div class="photo-status <?= $belowMin ? 'below-min' : ($atMax ? 'at-max' : 'ok') ?> mb-3">
        <div class="photo-status-count">
            <?= $count ?> of <?= $photoLimit === null ? 'unlimited' : (int)$photoLimit ?> photos uploaded
        </div>
        <?php if ($belowMin): ?>
            <p><strong>At least <?= PROFILE_PHOTO_MIN ?> photos are required</strong> before you can express interest in other seekers. Please add <?= $needed ?> more.</p>
        <?php elseif ($atMax): ?>
            <p>You've reached the photo limit for <?= e($plan['name'] ?? 'your plan') ?>. Upgrade for unlimited profile photos.</p>
        <?php elseif ($photoLimit === null): ?>
            <p>Your current plan includes unlimited profile photos.</p>
        <?php else: ?>
            <p>You have the minimum required. Add up to <?= (int)$photoLimit - $count ?> more if you'd like.</p>
        <?php endif; ?>
    </div>

    <div class="admin-card mb-3">
        <?php if ($atMax): ?>
            <p style="color: var(--c-muted); margin: 0;">Photo limit reached. Delete a photo below to add a new one.</p>
        <?php else: ?>
            <form method="post" action="/profile/photos" enctype="multipart/form-data" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
                <?= csrf_field() ?>
                <div class="field" style="flex:1; margin-bottom:0;">
                    <label>Add a new photo</label>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>
                    <span class="field-help">JPG, PNG, or WEBP up to 4MB. Your first photo becomes your primary automatically.</span>
                </div>
                <button class="btn btn-primary">Upload</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (!$photos): ?>
        <div class="admin-card text-center" style="padding: 4rem 2rem;">
            <p style="color: var(--c-muted);">No photos yet. Add at least <?= PROFILE_PHOTO_MIN ?> so other seekers can see who you are.</p>
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
