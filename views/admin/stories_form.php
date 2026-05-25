<?php /** @var array|null $story */
$isEdit = $story !== null;
$action = $isEdit ? '/admin/stories/' . (int)$story['id'] : '/admin/stories'; ?>
<div class="admin-head">
    <h1><?= $isEdit ? 'Edit Story' : 'New Story' ?></h1>
    <a href="/admin/stories" class="btn btn-ghost btn-sm">← Back</a>
</div>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="admin-card">
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field"><label>Couple name</label><input type="text" name="couple_name" value="<?= e($story['couple_name'] ?? '') ?>" required placeholder="Krishna &amp; Radha"></div>
        <div class="field"><label>Married on</label><input type="date" name="married_on" value="<?= e($story['married_on'] ?? '') ?>"></div>
    </div>
    <div class="field"><label>Their story</label><textarea name="story" rows="6" required><?= e($story['story'] ?? '') ?></textarea></div>
    <div class="field"><label>Photo</label><input type="file" name="photo_file" accept="image/*">
        <?php if (!empty($story['photo'])): ?>
            <input type="hidden" name="photo" value="<?= e($story['photo']) ?>">
            <span class="field-help">Current: <?= e($story['photo']) ?></span>
        <?php endif; ?>
    </div>
    <label style="display: flex; gap: .5rem; margin-bottom: 1rem;"><input type="checkbox" name="is_featured" value="1" <?= !empty($story['is_featured']) ? 'checked' : '' ?>> Feature on homepage</label>
    <button class="btn btn-primary"><?= $isEdit ? 'Save' : 'Create' ?></button>
</form>
