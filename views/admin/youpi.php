<?php /** @var array $values @var array $recent */
$enabled = ($values['youpi_enabled'] ?? '1') === '1';
$portalUrl = $values['youpi_portal_url'] ?: 'http://66.116.241.172:7000';
$adminUrl  = $values['youpi_admin_url'] ?: 'http://66.116.241.172:7010';
$appUrl    = rtrim($GLOBALS['CFG']['app']['url'] ?? '', '/');
$webhookUrl = ($appUrl ?: 'https://' . ($_SERVER['HTTP_HOST'] ?? 'spiritualshaadi.com')) . '/youpi/webhook';

$upiType = $values['payment_upi_type'] ?? 'merchant';
$mcc = $values['payment_upi_mcc'] ?? '5499';
$upiId = $values['payment_upi_id'] ?? '';
$payeeName = $values['payment_payee_name'] ?? 'SpiritualShaadi';
$qrImageUrl = $values['payment_upi_qr_url'] ?? '';

// Sample preview URI
$sampleCleanPayee = rawurlencode(preg_replace('/[^a-zA-Z0-9 ]/', '', substr($payeeName ?: 'SpiritualShaadi', 0, 25)));
// Universal standard preview URI (NPCI compliant, accepted by Google Pay, PhonePe, Paytm)
$previewUri = "upi://pay?pa=" . rawurlencode($upiId) . "&pn=" . $sampleCleanPayee . "&am=349.00&cu=INR&tn=SS_TEST_101";
$previewQr = "https://quickchart.io/qr?size=180&margin=2&text=" . rawurlencode($previewUri);
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
        <span class="pill green">Active</span> — Members clicking "Upgrade" or "Subscribe" on packages, add-ons, or verification are routed directly to the <strong>YouPI UPI Checkout</strong>.
    <?php else: ?>
        <span class="pill red">Disabled</span> — YouPI checkout is turned off. System falls back to Razorpay (if active) or manual UPI details.
    <?php endif; ?>
</div>

<form method="post" action="/admin/youpi">
    <?= csrf_field() ?>

    <div class="admin-card mb-3" style="border: 2px solid #22c55e;">
        <h3 style="color: #15803d;">1. Receiving UPI &amp; Merchant Configuration</h3>
        <p style="color: var(--c-muted);">Customer payments are deposited directly into this UPI / bank account.</p>

        <div class="form-grid" style="margin-top: 1rem;">
            <div class="field">
                <label><strong>Receiver UPI ID (VPA) *</strong></label>
                <input type="text" name="payment_upi_id" value="<?= e($values['payment_upi_id'] ?? '') ?>" placeholder="e.g. yourbusiness@okbizaxis or merchant@paytm" required style="font-weight: 600; font-size: 1.05rem;">
                <small style="color: #0369a1;">Enter your clean UPI ID. If you accidentally paste a full link (e.g. <code>upi://pay?pa=...</code>), it is automatically cleaned on save.</small>
            </div>
            <div class="field">
                <label>Payee / Business Name</label>
                <input type="text" name="payment_payee_name" value="<?= e($values['payment_payee_name'] ?? 'SpiritualShaadi') ?>" placeholder="e.g. SpiritualShaadi">
                <small style="color: var(--c-muted);">The business name customers see in PhonePe, Google Pay, or Paytm.</small>
            </div>
        </div>

        <div class="form-grid" style="margin-top: 1rem;">
            <div class="field">
                <label><strong>Account Type</strong></label>
                <select name="payment_upi_type" style="font-weight: 600;">
                    <option value="merchant" <?= $upiType === 'merchant' ? 'selected' : '' ?>>Merchant Account (P2M) — PhonePe Business, Paytm for Business, GPay Business, BharatPe, Current A/C</option>
                    <option value="personal" <?= $upiType === 'personal' ? 'selected' : '' ?>>Personal Account (P2P) — Individual Savings A/C (@axl, @ybl, @okhdfcbank)</option>
                </select>
                <small style="color: var(--c-muted);">Merchant accounts support dynamic amount locking and NPCI merchant compliance.</small>
            </div>
            <div class="field">
                <label>Merchant Category Code (MCC)</label>
                <input type="text" name="payment_upi_mcc" value="<?= e($mcc) ?>" placeholder="5499" maxlength="4">
                <small style="color: #b45309;"><strong>Crucial for Merchant QRs:</strong> NPCI strictly requires a 4-digit MCC (e.g. <strong>5499</strong> for general services, <strong>8699</strong> for memberships, <strong>7299</strong> for personal services). Without MCC, PhonePe and Paytm reject merchant QR codes as <em>"Invalid QR Code"</em>.</small>
            </div>
        </div>

        <div class="field" style="margin-top: 1rem;">
            <label>Official Merchant QR Code Image URL (Optional / Highly Recommended)</label>
            <input type="url" name="payment_upi_qr_url" value="<?= e($qrImageUrl) ?>" placeholder="https://yourdomain.com/uploads/merchant-qr.png">
            <small style="color: var(--c-muted);">If you downloaded an official QR code image from your PhonePe Business / Paytm Business app, paste its image URL here. When set, this official QR image is displayed directly to customers on the checkout page.</small>
        </div>

        <div style="margin-top: 1.25rem; background: #fefce8; border: 1px solid #fef08a; border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.88rem; color: #854d0e; line-height: 1.45;">
            <strong>⚠️ Important Notice Regarding Self-Payment Testing:</strong><br>
            All UPI apps in India (PhonePe, Google Pay, Paytm, BHIM) <strong>strictly prohibit</strong> transferring money to yourself (where sender and receiver are the same person / phone number / PAN). If you try to test checkout on your own phone using an account linked to this UPI ID, PhonePe/GPay will reject the transaction with <em>"Something went wrong"</em> or <em>"Payment to self not allowed"</em>. <strong>Always test using a friend's phone or a secondary bank account.</strong>
        </div>

        <?php if (!empty($upiId)): ?>
            <div style="margin-top: 1.25rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
                <div>
                    <img src="<?= e($previewQr) ?>" alt="Test QR Preview" style="width: 140px; height: 140px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff;">
                </div>
                <div style="flex: 1; min-width: 240px;">
                    <div style="font-size: 0.82rem; font-weight: 700; color: #475569; text-transform: uppercase;">Live QR &amp; Intent Preview:</div>
                    <code style="display: block; font-size: 0.78rem; word-break: break-all; margin: 0.35rem 0; color: #0369a1; background: #fff; padding: 0.5rem; border-radius: 6px; border: 1px solid #e2e8f0;"><?= e($previewUri) ?></code>
                    <small style="color: #64748b;">Scan this test QR from a secondary phone to verify that PhonePe/Google Pay recognizes your merchant VPA cleanly without any "Invalid QR" error.</small>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="admin-card mb-3">
        <h3>2. Gateway Connectivity</h3>
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
        <h3>3. Webhook &amp; Security</h3>
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
        <h3>4. Enable Checkout</h3>
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
                    <th>Action</th>
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
                    <td>
                        <?php if (!empty($p['gateway_payment_id'])): ?>
                            <code style="font-size: .8rem; color: #0284c7; font-weight:700;"><?= e($p['gateway_payment_id']) ?></code>
                        <?php else: ?>
                            <span style="color: var(--c-muted);">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($p['status'] !== 'paid'): ?>
                            <form method="post" action="/admin/youpi/approve/<?= (int)$p['id'] ?>" style="display:inline;" onsubmit="return confirm('Activate plan for <?= e(addslashes($p['user_name'] ?? 'User')) ?>? Confirm you received ₹<?= number_format((float)$p['amount'], 2) ?> in your bank.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm" style="background:#059669; color:#fff; padding:0.25rem 0.6rem; font-size:0.78rem; font-weight:600; border:none; border-radius:4px; cursor:pointer;">✓ Approve</button>
                            </form>
                        <?php else: ?>
                            <span style="color:#059669; font-size:0.8rem; font-weight:600;">✓ Active</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>