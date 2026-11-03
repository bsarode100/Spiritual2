<?php /** @var array $payment @var array $user */
$utr = $payment['gateway_payment_id'] ?? '';
$amount = number_format((float)$payment['amount'], 2);
$packageName = $payment['package_name'] ?? 'Membership Plan';
$orderRef = $payment['gateway_order_id'] ?? ('SS_' . $payment['id']);
$createdTime = !empty($payment['created_at']) ? date('M j, Y · h:i A', strtotime($payment['created_at'])) : date('M j, Y · h:i A');
$supportPhone = setting('payment_contact_phone', '7744922099');
$supportCleanPhone = preg_replace('/\D/', '', $supportPhone);
if (strlen($supportCleanPhone) === 10) {
    $supportCleanPhone = '91' . $supportCleanPhone;
}
$waText = rawurlencode("Namaste SpiritualShaadi Support, I have submitted payment of ₹{$amount} for {$packageName} with UTR: {$utr}. Please assist with my membership activation.");
$waUrl = "https://wa.me/{$supportCleanPhone}?text={$waText}";
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, interactive-widget=resizes-content">
<meta name="theme-color" content="#FAF7F2">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<title>Payment Verification — SpiritualShaadi</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
    background: #FAF7F2;
    color: #0F172A;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    -webkit-tap-highlight-color: transparent;
    -webkit-font-smoothing: antialiased;
}

/* Sticky App Header */
.app-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: #FFFFFF;
    border-bottom: 1px solid #E2E8F0;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.app-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.75rem;
    background: #FAF7F2;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    color: #1E293B;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.15s;
}
.app-back-btn:active { background: #E2E8F0; }
.app-brand {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none;
}
.app-brand-title {
    font-size: 0.95rem;
    font-weight: 800;
    color: #8B1E3F;
    letter-spacing: -0.01em;
}
.app-header-badge {
    font-size: 0.72rem;
    color: #047857;
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    padding: 0.25rem 0.55rem;
    border-radius: 9999px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

/* Page Container - 100% Fit to Screen */
.page-container {
    width: 100%;
    max-width: 480px;
    margin: 0 auto;
    padding: 1.25rem 1rem 3.5rem;
}

/* Status Hero Card */
.status-card {
    background: #FFFFFF;
    border-radius: 20px;
    padding: 1.85rem 1.35rem;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
    text-align: center;
}

/* Auspicious Pulsating Amber Icon */
.status-icon-wrap {
    width: 72px;
    height: 72px;
    background: #FEF3C7;
    border: 2.5px solid #F59E0B;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.1rem;
    margin: 0 auto 1.15rem;
    box-shadow: 0 0 0 8px rgba(245, 158, 11, 0.15);
    animation: pulseAura 2s infinite ease-in-out;
}
@keyframes pulseAura {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.15); }
    50% { transform: scale(1.04); box-shadow: 0 0 0 12px rgba(245, 158, 11, 0); }
}

.status-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #1E293B;
    margin-bottom: 0.45rem;
    line-height: 1.25;
}
.status-subtitle {
    font-size: 0.88rem;
    color: #64748B;
    line-height: 1.5;
    margin-bottom: 1.25rem;
}

/* Pulsing Status Pill */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: #FEF3C7;
    color: #92400E;
    font-size: 0.82rem;
    font-weight: 700;
    padding: 0.4rem 0.85rem;
    border-radius: 9999px;
    border: 1px solid #FDE68A;
    margin-bottom: 1.25rem;
}
.pulse-dot {
    width: 8px;
    height: 8px;
    background: #F59E0B;
    border-radius: 50%;
    animation: blink 1.2s infinite;
}
@keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

/* Voucher Details Box */
.receipt-box {
    background: #FAF7F2;
    border: 1px solid #E5DECE;
    border-radius: 14px;
    padding: 1.15rem 1.15rem;
    text-align: left;
    margin-bottom: 1.25rem;
}
.receipt-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.65rem;
    margin-bottom: 0.65rem;
    border-bottom: 1px solid #E5DECE;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #8B1E3F;
}
.receipt-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.45rem 0;
    font-size: 0.88rem;
    border-bottom: 1px dashed #E2E8F0;
}
.receipt-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.receipt-label {
    color: #64748B;
    font-weight: 500;
}
.receipt-val {
    font-weight: 700;
    color: #0F172A;
    text-align: right;
}
.receipt-val.amount {
    color: #047857;
    font-size: 1rem;
}

/* Copy UTR chip */
.utr-chip-wrap {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
}
.btn-copy-sm {
    background: none;
    border: none;
    color: #4338CA;
    font-size: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    padding: 0 0.15rem;
}
.btn-copy-sm:hover { text-decoration: underline; }

/* Matrimonial Verification Stepper (Shaadi.com Standard) */
.stepper-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 1rem 1.15rem;
    margin-bottom: 1.25rem;
    text-align: left;
}
.stepper-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.75rem;
}
.step-item {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
    position: relative;
    padding-bottom: 0.85rem;
}
.step-item:last-child { padding-bottom: 0; }
.step-item::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 22px;
    bottom: 0;
    width: 2px;
    background: #E2E8F0;
}
.step-item:last-child::before { display: none; }
.step-badge {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
    flex-shrink: 0;
    position: relative;
    z-index: 2;
}
.step-badge.done { background: #10B981; color: #FFFFFF; }
.step-badge.active { background: #F59E0B; color: #FFFFFF; animation: blink 1.5s infinite; }
.step-badge.pending { background: #E2E8F0; color: #94A3B8; }
.step-content {
    font-size: 0.84rem;
    color: #334155;
    line-height: 1.35;
}
.step-content strong { color: #0F172A; display: block; font-size: 0.86rem; }

/* Benefits Card: What Unlocks */
.benefits-card {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 1rem 1.15rem;
    margin-bottom: 1.25rem;
    text-align: left;
}
.benefits-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.65rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.benefits-list {
    list-style: none;
    font-size: 0.82rem;
    color: #475569;
    line-height: 1.6;
}
.benefits-list li {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin-bottom: 0.25rem;
}

/* WhatsApp Support Box (Shaadi.com Trust Standard) */
.support-card {
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    margin-bottom: 1.35rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    text-align: left;
}
.support-text {
    font-size: 0.8rem;
    color: #065F46;
    line-height: 1.35;
}
.btn-wa-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #059669;
    color: #FFFFFF;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.45rem 0.75rem;
    border-radius: 8px;
    text-decoration: none;
    white-space: nowrap;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(5,150,105,0.25);
}
.btn-wa-chip:active { background: #047857; }

/* Action Buttons */
.btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    width: 100%;
    min-height: 48px;
    background: linear-gradient(135deg, #D97706 0%, #B45309 100%);
    color: #FFFFFF;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0.85rem 1.25rem;
    border-radius: 12px;
    text-decoration: none;
    text-align: center;
    margin-bottom: 0.65rem;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
    transition: transform 0.1s, opacity 0.15s;
}
.btn-primary:active { transform: scale(0.98); opacity: 0.92; }
.btn-secondary {
    display: block;
    width: 100%;
    background: transparent;
    color: #64748B;
    font-weight: 600;
    font-size: 0.86rem;
    padding: 0.6rem;
    border-radius: 10px;
    text-decoration: none;
    text-align: center;
    transition: color 0.15s;
}
.btn-secondary:hover { color: #0F172A; }
</style>
</head>
<body>

<!-- Sticky Header with Secure Badge & Dashboard Link -->
<header class="app-header">
    <a href="/dashboard" class="app-back-btn">← Dashboard</a>
    <div class="app-brand">
        <span style="font-size: 1.2rem;">🪷</span>
        <span class="app-brand-title">SpiritualShaadi</span>
    </div>
    <div class="app-header-badge">
        <span>🔒</span>
        <span>Secure</span>
    </div>
</header>

<main class="page-container">
    <div class="status-card">
        <div class="status-icon-wrap" id="statusIcon">⏳</div>
        <h1 class="status-title" id="statusTitle">Payment Under Verification</h1>
        <p class="status-subtitle" id="statusSub">
            We have received your payment details. Your membership is being validated against our bank credit records and will activate automatically.
        </p>

        <div class="status-pill" id="statusBadge">
            <span class="pulse-dot"></span>
            <span>Bank Verification in Progress (5–15 mins)</span>
        </div>

        <!-- Receipt Box -->
        <div class="receipt-box">
            <div class="receipt-header">
                <span>Transaction Voucher</span>
                <span><?= e(setting('site_name', 'SpiritualShaadi')) ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Membership Plan</span>
                <span class="receipt-val"><?= e($packageName) ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Amount Paid</span>
                <span class="receipt-val amount">₹<?= e($amount) ?></span>
            </div>
            <?php if (!empty($utr)): ?>
                <div class="receipt-row">
                    <span class="receipt-label">Submitted UTR</span>
                    <span class="receipt-val">
                        <span class="utr-chip-wrap">
                            <code id="utrText" style="font-family: monospace; font-size: 0.85rem; font-weight: 700;"><?= e($utr) ?></code>
                            <button type="button" onclick="copyUtr()" id="copyUtrBtn" class="btn-copy-sm">Copy</button>
                        </span>
                    </span>
                </div>
            <?php endif; ?>
            <div class="receipt-row">
                <span class="receipt-label">Order Ref</span>
                <span class="receipt-val" style="font-size: 0.8rem; font-family: monospace; color: #475569;"><?= e($orderRef) ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Time</span>
                <span class="receipt-val" style="font-size: 0.8rem; color: #64748B;"><?= e($createdTime) ?></span>
            </div>
        </div>

        <!-- Stepper (Shaadi.com Standard) -->
        <div class="stepper-card">
            <div class="stepper-title">Verification Steps</div>
            <div class="step-item">
                <div class="step-badge done">✓</div>
                <div class="step-content">
                    <strong>Order Initiated</strong>
                    Plan selected and order generated
                </div>
            </div>
            <div class="step-item">
                <div class="step-badge done">✓</div>
                <div class="step-content">
                    <strong>Payment &amp; UTR Submitted</strong>
                    Bank transaction reference recorded
                </div>
            </div>
            <div class="step-item">
                <div class="step-badge active">⏳</div>
                <div class="step-content">
                    <strong>Bank Settlement Verification</strong>
                    Automatic statement credit matching in progress
                </div>
            </div>
            <div class="step-item">
                <div class="step-badge pending">⚪</div>
                <div class="step-content">
                    <strong>Membership Activation</strong>
                    All premium matchmaking privileges unlocked
                </div>
            </div>
        </div>

        <!-- Benefits Unlocked Upon Activation -->
        <div class="benefits-card">
            <div class="benefits-title">
                <span>🌟</span>
                <span>Unlocking for you shortly:</span>
            </div>
            <ul class="benefits-list">
                <li><span>📞</span> View verified contact numbers of shortlisted matches</li>
                <li><span>💬</span> Unlimited direct chats &amp; horoscope match compatibility</li>
                <li><span>🛡️</span> Verified Seeker Trust Shield on your profile</li>
                <li><span>🔝</span> Priority listing at the top of partner searches</li>
            </ul>
        </div>

        <!-- WhatsApp Support Helpline -->
        <div class="support-card">
            <div class="support-text">
                <strong>Need instant help?</strong><br>
                Our matrimonial support team is active on WhatsApp.
            </div>
            <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener" class="btn-wa-chip">
                <span>💬</span> WhatsApp
            </a>
        </div>

        <a href="/dashboard" class="btn-primary">Return to Dashboard</a>
        <a href="/billing" class="btn-secondary">View Billing &amp; Invoices →</a>
    </div>
</main>

<script>
function copyUtr() {
    const utr = document.getElementById('utrText').innerText;
    navigator.clipboard.writeText(utr).then(() => {
        const btn = document.getElementById('copyUtrBtn');
        btn.textContent = 'Copied! ✓';
        btn.style.color = '#059669';
        setTimeout(() => {
            btn.textContent = 'Copy';
            btn.style.color = '#4338CA';
        }, 2000);
    }).catch(() => {});
}

// Background auto-polling for instant activation
const paymentId = <?= json_encode((int)$payment['id']) ?>;
const successUrl = '/checkout/success?p=' + encodeURIComponent(paymentId);

setInterval(() => {
    fetch('/checkout/status/' + paymentId)
        .then(r => r.json())
        .then(data => {
            if (data && data.is_paid) {
                const icon = document.getElementById('statusIcon');
                const title = document.getElementById('statusTitle');
                const sub = document.getElementById('statusSub');
                const badge = document.getElementById('statusBadge');
                
                if (icon) {
                    icon.textContent = '🎉';
                    icon.style.background = '#ECFDF5';
                    icon.style.borderColor = '#10B981';
                }
                if (title) title.textContent = 'Payment Confirmed!';
                if (sub) sub.textContent = 'Your plan has been activated successfully. Redirecting to your dashboard...';
                if (badge) {
                    badge.innerHTML = '<span>✓</span> <span>Membership Activated!</span>';
                    badge.style.background = '#ECFDF5';
                    badge.style.color = '#047857';
                    badge.style.borderColor = '#A7F3D0';
                }

                setTimeout(() => {
                    window.location.href = successUrl;
                }, 1200);
            }
        })
        .catch(() => {});
}, 3000);
</script>

</body>
</html>
