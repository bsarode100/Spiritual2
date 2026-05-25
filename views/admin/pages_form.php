<?php /** @var array $page */ ?>
<div class="admin-head">
    <h1>Edit Page — <?= e($page['slug']) ?></h1>
    <a href="/admin/pages" class="btn btn-ghost btn-sm">← Back</a>
</div>
<form method="post" action="/admin/pages/<?= (int)$page['id'] ?>" class="admin-card">
    <?= csrf_field() ?>
    <div class="field"><label>Title</label><input type="text" name="title" value="<?= e($page['title']) ?>"></div>
    <div class="field"><label>Body (HTML allowed)</label>
        <textarea name="body" rows="20" style="font-family: monospace; font-size: .92rem;"><?= e($page['body']) ?></textarea>
    </div>
    <label style="display: flex; gap: .5rem; margin: 1rem 0;"><input type="checkbox" name="published" value="1" <?= $page['published'] ? 'checked' : '' ?>> Published</label>
    <button class="btn btn-primary">Save</button>
</form>
