<?php /** @var array|null $post */
$isEdit = $post !== null;
$action = $isEdit ? '/admin/blog/' . (int)$post['id'] : '/admin/blog'; ?>
<div class="admin-head">
    <h1><?= $isEdit ? 'Edit Post' : 'New Post' ?></h1>
    <a href="/admin/blog" class="btn btn-ghost btn-sm">← Back</a>
</div>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="admin-card">
    <?= csrf_field() ?>
    <div class="field"><label>Title</label><input type="text" name="title" value="<?= e($post['title'] ?? '') ?>" required></div>
    <div class="form-grid">
        <div class="field"><label>Slug</label><input type="text" name="slug" value="<?= e($post['slug'] ?? '') ?>" placeholder="auto-generated from title"></div>
        <div class="field"><label>Category</label><input type="text" name="category" value="<?= e($post['category'] ?? 'General') ?>"></div>
        <div class="field"><label>Author</label><input type="text" name="author_name" value="<?= e($post['author_name'] ?? 'Editorial Team') ?>"></div>
        <div class="field"><label>Cover image</label><input type="file" name="cover" accept="image/*">
            <?php if (!empty($post['cover_image'])): ?>
                <input type="hidden" name="cover_image" value="<?= e($post['cover_image']) ?>">
                <span class="field-help">Current: <?= e($post['cover_image']) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="field"><label>Excerpt</label><textarea name="excerpt" rows="2"><?= e($post['excerpt'] ?? '') ?></textarea></div>
    <div class="field"><label>Body (HTML allowed)</label><textarea name="body" rows="16" style="font-family: monospace; font-size: .92rem;"><?= e($post['body'] ?? '') ?></textarea></div>
    <label style="display: flex; align-items: center; gap: .5rem; margin: 1rem 0;">
        <input type="checkbox" name="published" value="1" <?= !$isEdit || $post['published'] ? 'checked' : '' ?>>
        <span>Publish (visible on the public blog)</span>
    </label>
    <button class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Publish' ?></button>
</form>
