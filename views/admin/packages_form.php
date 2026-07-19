<?php /** @var array|null $pkg */
$isEdit = $pkg !== null;
$action = $isEdit ? '/admin/packages/' . (int)$pkg['id'] : '/admin/packages';
$checked = fn(string $key, int $default = 0) => ((int)($pkg[$key] ?? $default) === 1) ? 'checked' : '';
?>
<div class="admin-head">
    <h1><?= $isEdit ? 'Edit Membership Plan' : 'New Membership Plan' ?></h1>
    <a href="/admin/packages" class="btn btn-ghost btn-sm">Back</a>
</div>

<form method="post" action="<?= $action ?>" class="admin-card">
    <?= csrf_field() ?>

    <h3>Plan Identity</h3>
    <div class="form-grid">
        <div class="field"><label>Name</label><input type="text" name="name" value="<?= e($pkg['name'] ?? '') ?>" required></div>
        <div class="field"><label>Slug</label><input type="text" name="slug" value="<?= e($pkg['slug'] ?? '') ?>" placeholder="starter, divine, soul_elite"></div>
        <div class="field full"><label>Tagline</label><input type="text" name="tagline" value="<?= e($pkg['tagline'] ?? '') ?>"></div>
        <div class="field"><label>Ribbon</label><input type="text" name="ribbon" value="<?= e($pkg['ribbon'] ?? '') ?>" placeholder="MOST POPULAR"></div>
        <div class="field"><label>Savings Badge</label><input type="text" name="savings_badge" value="<?= e($pkg['savings_badge'] ?? '') ?>" placeholder="Save 15%"></div>
    </div>

    <h3 class="mt-4">Pricing</h3>
    <div class="form-grid-3">
        <div class="field"><label>Price</label><input type="number" name="price" value="<?= e($pkg['price'] ?? 0) ?>" step="0.01"></div>
        <div class="field"><label>Currency</label><input type="text" name="currency" value="<?= e($pkg['currency'] ?? 'INR') ?>"></div>
        <div class="field"><label>Monthly Display</label><input type="number" name="monthly_display" value="<?= e($pkg['monthly_display'] ?? '') ?>" step="0.01" placeholder="333"></div>
        <div class="field"><label>Duration Months</label><input type="number" name="duration_months" value="<?= e($pkg['duration_months'] ?? 1) ?>" min="0"></div>
        <div class="field"><label>Duration Days</label><input type="number" name="duration_days" value="<?= e($pkg['duration_days'] ?? 30) ?>" min="0"><span class="field-help">Use 36500 for lifetime/free.</span></div>
        <div class="field"><label>Display Order</label><input type="number" name="display_order" value="<?= e($pkg['display_order'] ?? 0) ?>"></div>
    </div>

    <h3 class="mt-4">Limits</h3>
    <div class="form-grid-3">
        <div class="field"><label>Interests / Month</label><input type="number" name="interests_per_month" value="<?= e($pkg['interests_per_month'] ?? 10) ?>"><span class="field-help">0 = unlimited.</span></div>
        <div class="field"><label>Contact Views</label><input type="number" name="contacts_limit" value="<?= e($pkg['contacts_limit'] ?? 0) ?>"><span class="field-help">0 = unlimited for paid plans, none for free.</span></div>
        <div class="field"><label>Shortlist Limit</label><input type="number" name="shortlist_limit" value="<?= e($pkg['shortlist_limit'] ?? 20) ?>"><span class="field-help">0 = unlimited.</span></div>
        <div class="field"><label>Boosts / Month</label><input type="number" name="boosts_per_month" value="<?= e($pkg['boosts_per_month'] ?? 0) ?>"></div>
        <div class="field"><label>Featured Days</label><input type="number" name="featured_days" value="<?= e($pkg['featured_days'] ?? 0) ?>"></div>
        <div class="field"><label>Search Priority</label>
            <select name="priority_rank">
                <?php foreach ([1=>'Lowest',2=>'Standard',3=>'Medium',4=>'High',5=>'Highest'] as $rank => $label): ?>
                    <option value="<?= $rank ?>" <?= (int)($pkg['priority_rank'] ?? 1) === $rank ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <h3 class="mt-4">Feature Permissions</h3>
    <div class="admin-check-grid">
        <label><input type="checkbox" name="unlimited_photos" value="1" <?= $checked('unlimited_photos') ?>> Unlimited profile photos</label>
        <label><input type="checkbox" name="unlimited_search" value="1" <?= $checked('unlimited_search') ?>> Unlimited search</label>
        <label><input type="checkbox" name="advanced_search" value="1" <?= $checked('advanced_search') ?>> Advanced search filters</label>
        <label><input type="checkbox" name="see_who_viewed" value="1" <?= $checked('see_who_viewed') ?>> See who viewed profile</label>
        <label><input type="checkbox" name="see_who_shortlisted" value="1" <?= $checked('see_who_shortlisted') ?>> See who shortlisted you</label>
        <label><input type="checkbox" name="premium_badge" value="1" <?= $checked('premium_badge') ?>> Premium membership badge</label>
        <label><input type="checkbox" name="always_featured" value="1" <?= $checked('always_featured') ?>> Always featured while active</label>
        <label><input type="checkbox" name="highlighted" value="1" <?= $checked('highlighted') ?>> Highlight on pricing page</label>
        <label><input type="checkbox" name="is_active" value="1" <?= $checked('is_active', 1) ?>> Show on website</label>
    </div>

    <div class="form-grid mt-3">
        <div class="field"><label>Customer Support</label><input type="text" name="support_tier" value="<?= e($pkg['support_tier'] ?? 'Email') ?>"></div>
        <div class="field"><label>Match Suggestions</label><input type="text" name="match_suggestions" value="<?= e($pkg['match_suggestions'] ?? 'Basic') ?>"></div>
    </div>

    <div class="field">
        <label>Features (one per line)</label>
        <textarea name="features" rows="10"><?= e($pkg['features'] ?? '') ?></textarea>
    </div>

    <button class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Create Plan' ?></button>
</form>
