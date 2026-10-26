<?php /** @var array $pkg @var array $payment @var string $portal_url @var array $user @var string $kind */
$upiId = trim(setting('payment_upi_id') ?: setting('youpi_vpa') ?: '');
$payeeName = trim(setting('payment_payee_name') ?: setting('site_name', 'SpiritualShaadi'));
$isConfigured = !empty($upiId) && !str_contains($upiId, 'example.com') && $upiId !== 'spiritualmatrimony@upi';

$amount = number_format((float)$pkg['price'], 2, '.', '');
$orderRef = $payment['gateway_order_id'] ?? ('SS' . $payment['id']);
$cleanNote = preg_replace('/[^a-zA-Z0-9]/', '', $orderRef);
if (strlen($cleanNote) > 20) {
    $cleanNote = substr($cleanNote, 0, 20);
}

// NPCI standard parameters
$encodedUpi = rawurlencode($upiId);
$encodedPayee = rawurlencode(substr($payeeName, 0, 25));
$upiParams = "pa={$encodedUpi}&pn={$encodedPayee}&am={$amount}&cu=INR&tn={$cleanNote}&tr={$cleanNote}";

$upiUriGeneric = "upi://pay?{$upiParams}";
$upiUriPhonepe = "phonepe://pay?{$upiParams}";
$upiUriPaytm   = "paytmmp://pay?{$upiParams}";
$upiUriGpay    = "upi://pay?{$upiParams}";

$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=8&data=" . urlencode($upiUriGeneric);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Pay ₹<?= e(number_format((float)$pkg['price'], 0)) ?> — <?= e($payeeName) ?></title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    background: #f1f5f9;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    color: #0f172a;
    -webkit-tap-highlight-color: transparent;
    min-height: 100vh;
}

/* Sticky App Navigation Header */
.app-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.app-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.75rem;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    color: #1e293b;
    font-size: 0.88rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.15s;
}
.app-back-btn:active { background: #e2e8f0; }
.app-header-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
}
.app-header-badge {
    font-size: 0.75rem;
    color: #059669;
    background: #ecfdf5;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-weight: 600;
}

/* Container */
.checkout-container {
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
    padding: 1rem 0.85rem 2.5rem;
}

.checkout-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.25rem 1rem;
    box-shadow: 0 4px 20px -2px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    text-align: center;
}

.plan-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.2rem;
}

.amount-container {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.75rem;
    margin: 0.85rem 0;
}
.amount-val {
    font-size: 2.1rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
}
.amount-sub {
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 0.25rem;
}

/* Config Warning if Admin hasn't set UPI ID */
.warning-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 0.85rem;
    color: #991b1b;
    font-size: 0.82rem;
    text-align: left;
    margin-bottom: 1rem;
    line-height: 1.4;
}

/* Direct UPI App Buttons */
.upi-section-title {
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    margin: 1rem 0 0.6rem;
    text-align: left;
}

.btn-app-grid {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}

.btn-upi-app {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0.85rem 1rem;
    border-radius: 12px;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 700;
    transition: transform 0.1s, opacity 0.15s;
    border: none;
    cursor: pointer;
}
.btn-upi-app:active { transform: scale(0.98); opacity: 0.9; }

.btn-gpay {
    background: #1a73e8;
    color: #ffffff;
}
.btn-phonepe {
    background: #5f259f;
    color: #ffffff;
}
.btn-paytm {
    background: #002e6e;
    color: #ffffff;
}
.btn-any-upi {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(5,150,105,0.25);
}

/* QR Code Toggle */
.qr-toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    width: 100%;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 0.65rem;
    margin-top: 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
}
.qr-wrapper {
    display: none;
    margin-top: 0.85rem;
    padding: 0.85rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}
.qr-wrapper img {
    width: 200px;
    height: 200px;
    border-radius: 8px;
    display: block;
    margin: 0 auto;
}

/* Polling Live Status */
.status-pill {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.1rem;
    padding: 0.55rem;
    background: #f8fafc;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #475569;
}
.pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22c55e;
    animation: pulse 1.6s infinite ease-in-out;
}
@keyframes pulse { 0% { transform: scale(0.85); opacity: 0.7; } 50% { transform: scale(1.4); opacity: 1; } 100% { transform: scale(0.85); opacity: 0.7; } }

/* UTR Manual Fallback */
.utr-section {
    margin-top: 1.25rem;
    border-top: 1px solid #f1f5f9;
    padding-top: 0.85rem;
    text-align: left;
}
.utr-btn-toggle {
    font-size: 0.82rem;
    color: #64748b;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}
.utr-box {
    display: none;
    margin-top: 0.65rem;
}
.utr-field {
    width: 100%;
    padding: 0.65rem 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}
.utr-submit-btn {
    width: 100%;
    padding: 0.65rem;
    background: #0f172a;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
}

.bottom-cancel {
    margin-top: 1.25rem;
    font-size: 0.82rem;
    color: #64748b;
    text-align: center;
}
.bottom-cancel a { color: #64748b; text-decoration: none; }
</style>
</head>
<body>

<!-- Mobile Friendly Top Header Bar -->
<header class="app-header">
    <a href="/packages" class="app-back-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Plans
    </a>
    <span class="app-header-title">Checkout</span>
    <span class="app-header-badge">🔒 Secure</span>
</header>

<main class="checkout-container">
    <div class="checkout-card">
        <h1 class="plan-title"><?= e($pkg['name']) ?></h1>
        
        <div class="amount-container">
            <div class="amount-val">₹<?= e(number_format((float)$pkg['price'], 0)) ?></div>
            <div class="amount-sub">
                <?php if (!empty($pkg['duration_days'])): ?>
                    Active for <?= (int)$pkg['duration_days'] ?> days · Instant Activation
                <?php else: ?>
                    <?= e($pkg['tagline'] ?? 'SpiritualShaadi Service') ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$isConfigured): ?>
            <div class="warning-box">
                <strong>⚠️ Merchant Setup Required:</strong><br/>
                Your receiver UPI ID is not configured. Please open <a href="/admin/youpi" style="color:#991b1b; font-weight:700;">Admin → ⚡ YouPI Gateway</a> and paste your active UPI ID (e.g. your Google Pay / PhonePe UPI ID).
            </div>
        <?php endif; ?>

        <!-- One-Tap UPI App Buttons -->
        <div class="upi-section-title">Tap to Pay via Installed App</div>
        <div class="btn-app-grid">
            <a href="<?= e($upiUriGeneric) ?>" class="btn-upi-app btn-any-upi">
                <span>⚡ Pay with Any UPI App</span>
                <span>→</span>
            </a>
            <a href="<?= e($upiUriPhonepe) ?>" class="btn-upi-app btn-phonepe">
                <span>PhonePe</span>
                <span>Pay ₹<?= e(number_format((float)$pkg['price'], 0)) ?></span>
            </a>
            <a href="<?= e($upiUriGpay) ?>" class="btn-upi-app btn-gpay">
                <span>Google Pay</span>
                <span>Pay ₹<?= e(number_format((float)$pkg['price'], 0)) ?></span>
            </a>
            <a href="<?= e($upiUriPaytm) ?>" class="btn-upi-app btn-paytm">
                <span>Paytm</span>
                <span>Pay ₹<?= e(number_format((float)$pkg['price'], 0)) ?></span>
            </a>
        </div>

        <!-- Collapsible QR Code for Desktop / Scanner -->
        <button type="button" class="qr-toggle-btn" onclick="toggleQr()">
            <span>📷</span>
            <span id="qrToggleText">Show QR Code (to scan from another phone)</span>
        </button>
        <div class="qr-wrapper" id="qrWrapper">
            <img src="<?= e($qrUrl) ?>" alt="UPI QR Code">
            <p style="font-size:0.75rem; color:#64748b; margin-top:0.4rem;">
                Scan using any UPI App
            </p>
        </div>

        <!-- Live Auto-Confirmation Status -->
        <div class="status-pill" id="statusPill">
            <span class="pulse-dot"></span>
            <span id="statusText">Waiting for payment confirmation…</span>
        </div>

        <!-- Fallback UTR Submission -->
        <div class="utr-section">
            <button type="button" class="utr-btn-toggle" onclick="toggleUtr()">
                <span>Paid but page didn't auto-redirect?</span>
                <span id="utrArrow">▼</span>
            </button>
            <form class="utr-box" id="utrBox" method="post" action="/checkout/verify-utr">
                <?= csrf_field() ?>
                <input type="hidden" name="payment_id" value="<?= (int)$payment['id'] ?>">
                <p style="font-size:0.75rem; color:#64748b; margin:0.35rem 0 0.5rem;">
                    Enter the 12-digit UTR / UPI Reference Number from your payment SMS:
                </p>
                <input type="text" name="utr" class="utr-field" placeholder="e.g. 429812345678" maxlength="25" required>
                <button type="submit" class="utr-submit-btn">Verify &amp; Activate Plan</button>
            </form>
        </div>
    </div>

    <div class="bottom-cancel">
        Ref: <code><?= e($orderRef) ?></code> · <a href="/packages">Cancel &amp; Go Back</a>
    </div>
</main>

<script>
const paymentId = <?= json_encode((int)$payment['id']) ?>;
const successUrl = '/checkout/success?p=' + encodeURIComponent(paymentId);
let pollTimer = null;

// Poll backend for real-time payment confirmation
function checkStatus() {
    fetch('/checkout/status/' + paymentId)
        .then(r => r.json())
        .then(data => {
            if (data && data.is_paid) {
                clearInterval(pollTimer);
                const pill = document.getElementById('statusPill');
                if (pill) {
                    pill.style.background = '#ecfdf5';
                    pill.style.color = '#065f46';
                    pill.innerHTML = '<strong>✓ Payment Confirmed! Activating your plan…</strong>';
                }
                setTimeout(() => {
                    window.location.href = successUrl;
                }, 1000);
            }
        })
        .catch(() => {});
}

pollTimer = setInterval(checkStatus, 2500);

function toggleQr() {
    const w = document.getElementById('qrWrapper');
    const t = document.getElementById('qrToggleText');
    if (w.style.display === 'block') {
        w.style.display = 'none';
        t.textContent = 'Show QR Code (to scan from another phone)';
    } else {
        w.style.display = 'block';
        t.textContent = 'Hide QR Code';
    }
}

function toggleUtr() {
    const b = document.getElementById('utrBox');
    const a = document.getElementById('utrArrow');
    if (b.style.display === 'block') {
        b.style.display = 'none';
        a.textContent = '▼';
    } else {
        b.style.display = 'block';
        a.textContent = '▲';
    }
}
</script>

</body>
</html>
