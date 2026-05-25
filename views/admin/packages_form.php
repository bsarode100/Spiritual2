<?php /** @var array|null $pkg */
$isEdit = $pkg !== null;
$action = $isEdit ? '/admin/packages/' . (int)$pkg['id'] : '/admin/packages'; ?>
<div class="admin-head">
    <h1><?= $isEdit ? 'Edit Package' : 'New Package' ?></h1>
    <a href="/admin/packages" class="btn btn-ghost btn-sm">← Back</a>
</div>
<form method="post" action="<?= $action ?>" class="admin-card">
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field"><label>Name</label><input type="text" name="name" value="<?= e($pkg['name'] ?? '') ?>" required></div>
        <div class="field"><label>Tagline</label><input type="text" name="tagline" value="<?= e($pkg['tagline'] ?? '') ?>"></div>
        <div class="field"><label>Price</label><input type="number" name="price" value="<?= e($pkg['price'] ?? 0) ?>" step="0.01"></div>
        <div class="field"><label>Currency</label><input type="text" name="currency" value="<?= e($pkg['currency'] ?? 'INR') ?>"></div>
        <div class="field"><label>Duration (days)</label><input type="number" name="duration_days" value="<?= e($pkg['duration_days'] ?? 90) ?>"></div>
        <div class="field"><label>Contacts limit</label><input type="number" name="contacts_limit" value="<?= e($pkg['contacts_limit'] ?? 0) ?>"><span class="field-help">0 = unlimited</span></div>
        <div class="field"><label>Display order</label><input type="number" name="display_order" value="<?= e($pkg['display_order'] ?? 0) ?>"></div>
    </div>
    <div class="field"><label>Features (one per line)</label><textarea name="features" rows="8"><?= e($pkg['features'] ?? '') ?></textarea></div>
    <label style="display: flex; gap: .5rem;"><input type="checkbox" name="highlighted" value="1" <?= !empty($pkg['highlighted']) ? 'checked' : '' ?>> Mark as "Most Popular"</label>
    <label style="display: flex; gap: .5rem; margin: .5rem 0 1rem;"><input type="checkbox" name="is_active" value="1" <?= !$isEdit || $pkg['is_active'] ? 'checked' : '' ?>> Show on website</label>
    <button class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Create' ?></button>
</form>
