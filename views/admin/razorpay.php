<?php /** @var array $values @var array $recent */
$enabled = $values['razorpay_enabled'] === '1';
$mode    = $values['razorpay_mode'] ?: 'test';
$hasKeys = $values['razorpay_key_id'] !== '' && $values['razorpay_key_secret'] !== '';
$appUrl  = rtrim($GLOBALS['CFG']['app']['url'] ?? '', '/');
?>
<div class="admin-head">
    <h1>Razorpay Gateway</h1>
    <a href="https://dashboard.razorpay.com/" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">↗ Razorpay Dashboard</a>
</div>

<div class="admin-card mb-3" style="border-left: 4px solid <?= $enabled && $hasKeys ? '#2e7d32' : '#c77700' ?>;">
    <strong>Status:</strong>
    <?php if ($enabled && $hasKeys): ?>
        <span class="pill green">Live</span> — members will see a "Pay with Razorpay" button on /packages, currently in <strong><?= e(strtoupper($mode)) ?></strong> mode.
    <?php elseif ($hasKeys): ?>
        <span class="pill red">Disabled</span> — keys saved but the toggle below is off. Members still see UPI/bank only.
    <?php else: ?>
        <span class="pill red">Not configured</span> — paste your Razorpay key_id and key_secret below to start.
    <?php endif; ?>
</div>

<form method="post" action="/admin/razorpay">
    <?= csrf_field() ?>

    <div class="admin-card mb-3">
        <h3>1. Get your keys</h3>
        <p style="color: var(--c-muted);">Sign in to <a href="https://dashboard.razorpay.com/" target="_blank" rel="noopener">Razorpay Dashboard</a> → Account &amp; Settings → <strong>API Keys</strong>. Click <em>Generate Test Key</em> (or Live Key after KYC). Razorpay shows the secret only once — copy both into the fields below.</p>

        <div class="form-grid">
            <div class="field">
                <label>Mode</label>
                <select name="razorpay_mode">
                    <option value="test" <?= $mode === 'test' ? 'selected' : '' ?>>Test (safe sandbox)</option>
                    <option value="live" <?= $mode === 'live' ? 'selected' : '' ?>>Live (real money)</option>
                </select>
                <small style="color: var(--c-muted);">Test keys start with <code>rzp_test_</code>, live keys with <code>rzp_live_</code>.</small>
            </div>
            <div class="field">
                <label>Key ID</label>
                <input type="text" name="razorpay_key_id" value="<?= e($values['razorpay_key_id']) ?>" placeholder="rzp_test_XXXXXXXXXXXX">
            </div>
            <div class="field">
                <label>Key Secret</label>
                <input type="password" name="razorpay_key_secret" value="<?= e($values['razorpay_key_secret']) ?>" placeholder="••••••••••••" autocomplete="new-password">
                <small style="color: var(--c-muted);">Stored encrypted-at-rest by your DB. Never exposed in JS.</small>
            </div>
        </div>
    </div>

    <div class="admin-card mb-3">
        <h3>2. Webhook (recommended)</h3>
        <p style="color: var(--c-muted);">In Razorpay Dashboard → Account &amp; Settings → <strong>Webhooks</strong> → "+ Add new webhook":</p>
        <ul style="color: var(--c-ink-soft);">
            <li><strong>URL:</strong> <code><?= e($appUrl) ?>/razorpay/webhook</code></li>
            <li><strong>Active events:</strong> <code>payment.captured</code> and <code>payment.failed</code></li>
            <li><strong>Secret:</strong> generate any random string (e.g. 32 chars), paste it here and in Razorpay</li>
        </ul>
        <div class="field">
            <label>Webhook secret</label>
            <input type="password" name="razorpay_webhook_secret" value="<?= e($values['razorpay_webhook_secret']) ?>" placeholder="any random 32-char string" autocomplete="new-password">
            <small style="color: var(--c-muted);">Used to verify webhook signatures. Without this we'll reject all webhook calls.</small>
        </div>
    </div>

    <div class="admin-card mb-3">
        <h3>3. Turn it on</h3>
        <label style="display: flex; gap: .5rem; align-items: center; font-weight: 600;">
            <input type="checkbox" name="razorpay_enabled" value="1" <?= $enabled ? 'checked' : '' ?>>
            Enable Razorpay checkout on /packages
        </label>
        <p style="color: var(--c-muted); margin-top: .5rem;">When enabled and keys are present, members will see a "Pay with Razorpay" button on the packages page next to the existing UPI/bank flow. Both flows can coexist.</p>
    </div>

    <button class="btn btn-primary btn-lg">Save Settings</button>
</form>

<div class="admin-card" style="margin-top: 2rem;">
    <h3>Recent payments</h3>
    <?php if (empty($recent)): ?>
        <p style="color: var(--c-muted);">No payments yet. Once a member completes checkout, transactions will appear here.</p>
    <?php else: ?>
        <table class="tbl">
            <thead><tr><th>Date</th><th>Member</th><th>Package</th><th>Amount</th><th>Status</th><th>Order ID</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $p): ?>
                <tr>
                    <td><?= e(date('d M Y H:i', strtotime($p['created_at']))) ?></td>
                    <td><?= e($p['user_name'] ?? '—') ?><br><small style="color: var(--c-muted);"><?= e($p['user_email'] ?? '') ?></small></td>
                    <td><?= e($p['package_name'] ?? '—') ?></td>
                    <td>₹<?= number_format((float)$p['amount'], 2) ?></td>
                    <td>
                        <span class="pill <?= $p['status'] === 'paid' ? 'green' : ($p['status'] === 'failed' ? 'red' : '') ?>"><?= e($p['status']) ?></span>
                    </td>
                    <td><code style="font-size: .82rem;"><?= e($p['gateway_order_id'] ?? '—') ?></code></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
