<?php /** @var array $values @var array $recent */
$enabled = ($values['youpi_enabled'] ?? '1') === '1';
$portalUrl = $values['youpi_portal_url'] ?: 'http://66.116.241.172:7000';
$adminUrl  = $values['youpi_admin_url'] ?: 'http://66.116.241.172:7010';
$appUrl    = rtrim($GLOBALS['CFG']['app']['url'] ?? '', '/');
$webhookUrl = ($appUrl ?: 'https://' . ($_SERVER['HTTP_HOST'] ?? 'spiritualshaadi.com')) . '/youpi/webhook';
?>
<div class="admin-head">
    <div>
        <h1>⚡ YouPI Direct UPI Gateway</h1>
        <p style="color: var(--c-muted); margin: 0.25rem 0 0;">Zero-fee direct bank-to-bank UPI payment system with real-time automated verification.</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="<?= e($adminUrl) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">↗ YouPI Merchant Admin (7010)</a>
        <a href="http://66.116.241.172:8090" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">🛠️ Control Center (8090)</a>
    </div>
</div>

<div class="admin-card mb-3" style="border-left: 4px solid <?= $enabled ? '#2e7d32' : '#c77700' ?>;">
    <strong>Gateway Status:</strong>
    <?php if ($enabled): ?>
        <span class="pill green">Active</span> — Members clicking "Subscribe" on packages, add-ons, or verification are routed directly to the high-converting <strong>YouPI UPI Checkout</strong>.
    <?php else: ?>
        <span class="pill red">Disabled</span> — YouPI checkout is turned off. System falls back to Razorpay (if active) or manual UPI details.
    <?php endif; ?>
</div>

<form method="post" action="/admin/youpi">
    <?= csrf_field() ?>

    <div class="admin-card mb-3">
        <h3>1. Gateway Connectivity</h3>
        <p style="color: var(--c-muted);">Configure the address of your self-hosted YouPI services running on your VPS.</p>

        <div class="form-grid">
            <div class="field">
                <label>Checkout Portal URL (Port 7000)</label>
                <input type="text" name="youpi_portal_url" value="<?= e($portalUrl) ?>" placeholder="http://66.116.241.172:7000">
                <small style="color: var(--c-muted);">Serves the modal SDK (<code>/sdk/youpi.js</code>) and interactive QR/intent payment screen.</small>
            </div>
            <div class="field">
                <label>Merchant Admin URL (Port 7010)</label>
                <input type="text" name="youpi_admin_url" value="<?= e($adminUrl) ?>" placeholder="http://66.116.241.172:7010">
                <small style="color: var(--c-muted);">Your merchant management panel where you configure VPA, UPI apps, and Observer devices.</small>
            </div>
        </div>
    </div>

    <div class="admin-card mb-3">
        <h3>2. Webhook & Security</h3>
        <p style="color: var(--c-muted);">When a customer pays, your Android Observer phone detects the SMS / UPI notification, and YouPI notifies SpiritualShaadi instantly via this webhook.</p>
        
        <div style="background: var(--c-cream-2, #faf6ee); padding: 1rem 1.25rem; border-radius: 8px; border: 1px dashed var(--c-border, #e2d9cc); margin-bottom: 1.25rem;">
            <p style="margin: 0 0 0.5rem 0; font-weight: 600; color: var(--c-maroon, #6b2118);">Copy this Webhook URL into YouPI Admin (Settings → Webhooks):</p>
            <code style="font-size: 0.95rem; font-weight: 700; color: #0284c7; background: #fff; padding: 0.35rem 0.65rem; border-radius: 4px; display: inline-block;"><?= e($webhookUrl) ?></code>
        </div>

        <div class="field">
            <label>Webhook Secret Key (Optional / Recommended)</label>
            <input type="password" name="youpi_webhook_secret" value="<?= e($values['youpi_webhook_secret'] ?? '') ?>" placeholder="Paste your webhook secret (e.g. SpiritualShaadi2026)" autocomplete="new-password">
            <small style="color: var(--c-muted);">If configured in both YouPI and here, SpiritualShaadi will verify HMAC-SHA256 signatures on every incoming payment notification.</small>
        </div>
    </div>

    <div class="admin-card mb-3">
        <h3>3. Enable Checkout</h3>
        <label style="display: flex; gap: 0.6rem; align-items: center; font-weight: 600; cursor: pointer;">
            <input type="checkbox" name="youpi_enabled" value="1" <?= $enabled ? 'checked' : '' ?> style="width: 1.2rem; height: 1.2rem; accent-color: var(--c-saffron, #D99420);">
            Enable YouPI UPI Checkout on SpiritualShaadi
        </label>
        <p style="color: var(--c-muted); margin-top: 0.5rem; font-size: 0.9rem;">
            When active, members on both web browsers and mobile app WebViews can pay seamlessly with PhonePe, Google Pay, Paytm, BHIM, Cred, or any UPI app.
        </p>
    </div>

    <button class="btn btn-primary btn-lg">Save Settings</button>
</form>

<div class="admin-card" style="margin-top: 2rem;">
    <h3>Recent YouPI UPI Transactions</h3>
    <?php if (empty($recent)): ?>
        <p style="color: var(--c-muted);">No YouPI payments recorded yet. Test transactions will appear here automatically.</p>
    <?php else: ?>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Item / Package</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Order Ref</th>
                    <th>Bank UTR / ID</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recent as $p): ?>
                <tr>
                    <td><?= e(date('d M Y H:i', strtotime($p['created_at']))) ?></td>
                    <td>
                        <strong><?= e($p['user_name'] ?? '—') ?></strong><br>
                        <small style="color: var(--c-muted);"><?= e($p['user_email'] ?? '') ?></small>
                    </td>
                    <td><?= e($p['package_name'] ?? ucfirst($p['purchase_type'] ?? 'Package')) ?></td>
                    <td><strong style="color: #059669;">₹<?= number_format((float)$p['amount'], 2) ?></strong></td>
                    <td>
                        <span class="pill <?= $p['status'] === 'paid' ? 'green' : ($p['status'] === 'failed' ? 'red' : '') ?>">
                            <?= e(strtoupper($p['status'])) ?>
                        </span>
                    </td>
                    <td><code style="font-size: .8rem;"><?= e($p['gateway_order_id'] ?? '—') ?></code></td>
                    <td><code style="font-size: .8rem; color: #0284c7;"><?= e($p['gateway_payment_id'] ?? '—') ?></code></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
