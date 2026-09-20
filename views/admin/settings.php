<?php /** @var array $rows */
// Group by sensible prefix for the form
$groups = [
    'Brand'       => ['site_name','site_tagline','footer_about'],
    'Email & SMTP'=> ['mail_mailer','mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name'],
    'Hero'        => ['hero_heading','hero_subheading','hero_cta_text'],
    'About'       => ['about_short'],
    'Stats'       => ['stat_members','stat_marriages','stat_paths','stat_countries'],
    'Contact'     => ['contact_email','contact_phone','contact_address'],
    'Social'      => ['social_facebook','social_instagram','social_youtube'],
    'Payments'    => ['payment_payee_name','payment_upi_id','payment_upi_qr_url','payment_bank_name','payment_account_name','payment_account_number','payment_ifsc','payment_branch','payment_contact_phone','payment_contact_email','payment_instructions'],
];
$lookup = [];
foreach ($rows as $r) $lookup[$r['setting_key']] = $r['setting_value'];
// settings that exist but aren't grouped above
$known = array_merge(...array_values($groups));
$extras = array_diff(array_keys($lookup), $known);
if ($extras) $groups['Other'] = array_values($extras);
?>
<div class="admin-head">
    <h1>Site Settings</h1>
    <div style="display: flex; gap: .75rem; flex-wrap: wrap;">
        <form method="post" action="/admin/test-mail" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-ghost btn-sm">✉️ Test Current SMTP Delivery</button>
        </form>
        <a href="/admin/payment-details" class="btn btn-ghost btn-sm">💳 Dedicated Payment Details editor →</a>
    </div>
</div>
<form method="post" action="/admin/settings">
    <?= csrf_field() ?>
    <?php foreach ($groups as $group => $keys): ?>
        <div class="admin-card mb-3">
            <h3><?= e($group) ?></h3>
            <?php foreach ($keys as $k):
                $v = $lookup[$k] ?? '';
                $isLong = in_array($k, ['hero_subheading','footer_about','about_short','contact_address','payment_instructions']);
                $helpText = match($k) {
                    'mail_host' => 'e.g. smtp.gmail.com',
                    'mail_port' => '587 (TLS) or 465 (SSL)',
                    'mail_username' => 'Your full email (e.g. you@gmail.com)',
                    'mail_password' => '16-character Google App Password (from myaccount.google.com/apppasswords)',
                    'mail_encryption' => 'tls or ssl',
                    'mail_from_address' => 'Must match or be an authorized alias of your mail username',
                    default => null,
                };
            ?>
                <div class="field">
                    <label><?= e(ucfirst(str_replace('_',' ', $k))) ?></label>
                    <?php if ($isLong): ?>
                        <textarea name="settings[<?= e($k) ?>]" rows="3"><?= e($v) ?></textarea>
                    <?php elseif ($k === 'mail_password'): ?>
                        <input type="password" name="settings[<?= e($k) ?>]" value="<?= e($v) ?>" autocomplete="new-password">
                    <?php else: ?>
                        <input type="text" name="settings[<?= e($k) ?>]" value="<?= e($v) ?>">
                    <?php endif; ?>
                    <?php if ($helpText): ?>
                        <small style="color: var(--c-muted); font-size: 0.8rem;"><?= e($helpText) ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="admin-card mb-3">
        <h3>Add a new setting</h3>
        <p style="color: var(--c-muted);">Useful for custom keys — anything you reference in views via <code>setting('key')</code>.</p>
        <div class="form-grid">
            <div class="field"><label>Key</label><input type="text" name="new_key" placeholder="e.g. featured_quote"></div>
            <div class="field"><label>Value</label><input type="text" name="new_val"></div>
        </div>
    </div>

    <button class="btn btn-primary btn-lg">Save All Settings</button>
</form>

<script>
// Allow adding ad-hoc settings via the same form
document.querySelector('form').addEventListener('submit', e => {
    const k = document.querySelector('[name="new_key"]').value.trim();
    const v = document.querySelector('[name="new_val"]').value;
    if (k) {
        const i = document.createElement('input');
        i.type = 'hidden'; i.name = 'settings[' + k + ']'; i.value = v;
        e.target.appendChild(i);
    }
});
</script>
