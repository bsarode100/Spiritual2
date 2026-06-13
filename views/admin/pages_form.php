<?php /** @var ?array $page */
$isNew = empty($page);
$action = $isNew ? '/admin/pages' : ('/admin/pages/' . (int)$page['id']);
$title  = $isNew ? '' : $page['title'];
$slug   = $isNew ? '' : $page['slug'];
$body   = $isNew ? '' : $page['body'];
$pub    = $isNew ? 1 : (int)$page['published'];
$protected = ['about','privacy','terms','contact'];
$canDelete = !$isNew && !in_array($slug, $protected, true);
?>
<div class="admin-head">
    <h1><?= $isNew ? 'New Page' : ('Edit Page — ' . e($slug)) ?></h1>
    <a href="/admin/pages" class="btn btn-ghost btn-sm">← Back</a>
</div>
<form method="post" action="<?= e($action) ?>" class="admin-card">
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field">
            <label>Title</label>
            <input type="text" name="title" value="<?= e($title) ?>" required>
        </div>
        <div class="field">
            <label>Slug <?= $isNew ? '' : '(cannot be changed)' ?></label>
            <?php if ($isNew): ?>
                <input type="text" name="slug" value="<?= e($slug) ?>" placeholder="e.g. refund-policy (auto-filled from title if blank)">
                <small style="color: var(--c-muted);">Public URL will be <code>/page/your-slug</code>. Use lowercase letters, numbers and hyphens only.</small>
            <?php else: ?>
                <input type="text" value="<?= e($slug) ?>" disabled>
                <small style="color: var(--c-muted);">Public URL: <a href="/page/<?= e($slug) ?>" target="_blank"><code>/page/<?= e($slug) ?></code></a></small>
            <?php endif; ?>
        </div>
    </div>
    <div class="field">
        <label>Body (HTML allowed)</label>
        <textarea name="body" rows="20" style="font-family: monospace; font-size: .92rem;"><?= e($body) ?></textarea>
        <small style="color: var(--c-muted);">You can paste full HTML — <code>&lt;h2&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;a href&gt;</code>, etc. Content is rendered as-is to the public page.</small>
    </div>
    <label style="display: flex; gap: .5rem; margin: 1rem 0;">
        <input type="checkbox" name="published" value="1" <?= $pub ? 'checked' : '' ?>> Published (visible to the public)
    </label>
    <div style="display: flex; gap: .5rem; align-items: center;">
        <button class="btn btn-primary"><?= $isNew ? 'Create Page' : 'Save Changes' ?></button>
        <?php if (!$isNew): ?>
            <a href="/page/<?= e($slug) ?>" target="_blank" class="btn btn-ghost">↗ Preview</a>
        <?php endif; ?>
    </div>
</form>

<?php if ($canDelete): ?>
<form method="post" action="/admin/pages/<?= (int)$page['id'] ?>/delete" class="admin-card" style="margin-top: 1rem; border: 1px solid #f3d6d6;"
      onsubmit="return confirm('Delete this page? This cannot be undone.');">
    <?= csrf_field() ?>
    <h3 style="color: #b3261e;">Danger zone</h3>
    <p style="color: var(--c-ink-soft);">Permanently delete this page. The public URL will start returning 404.</p>
    <button class="btn" style="background: #b3261e; color: white;">Delete page</button>
</form>
<?php endif; ?>
