<?php /** @var array $pkg @var array $payment @var string $portal_url @var array $user @var string $kind */
$upiId = setting('payment_upi_id', 'spiritualmatrimony@upi');
if (empty($upiId)) {
    $upiId = 'spiritualmatrimony@upi';
}
$amount = number_format((float)$pkg['price'], 2, '.', '');
$orderRef = $payment['gateway_order_id'] ?? ('SS_' . $payment['id']);
$siteName = setting('site_name', 'SpiritualShaadi');

// Standard NPCI UPI URI Format
$upiUri = "upi://pay?pa=" . urlencode($upiId) . "&pn=" . urlencode($siteName) . "&am=" . urlencode($amount) . "&cu=INR&tn=" . urlencode("Plan " . $pkg['name'] . " Ref " . $orderRef) . "&tr=" . urlencode($orderRef);
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=280x280&margin=8&data=" . urlencode($upiUri);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pay ₹<?= e($amount) ?> for <?= e($pkg['name']) ?> — <?= e($siteName) ?></title>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<style>
:root {
    --upi-green: #059669;
    --upi-green-bg: #ecfdf5;
}
body { background: #f8fafc; font-family: system-ui, -apple-system, sans-serif; color: #1e293b; margin: 0; }
.checkout-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem 1rem; }
.checkout-card { max-width: 480px; width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.12); padding: 2rem; text-align: center; border: 1px solid #e2e8f0; }
.secure-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.85rem; background: var(--upi-green-bg); border: 1px solid #a7f3d0; color: #065f46; font-size: 0.82rem; font-weight: 700; border-radius: 9999px; margin-bottom: 0.85rem; }
h1 { margin: 0.25rem 0; font-size: 1.5rem; color: #0f172a; }
.amount-box { margin: 1rem 0; padding: 0.85rem; background: #f1f5f9; border-radius: 12px; }
.amount { font-size: 2.25rem; font-weight: 800; color: #0f172a; }
.amount-note { font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; }
.qr-frame { background: white; padding: 0.85rem; border-radius: 16px; border: 2px dashed #cbd5e1; display: inline-block; margin: 0.85rem auto; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
.qr-frame img { display: block; border-radius: 8px; width: 220px; height: 220px; }
.upi-apps { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin: 1.25rem 0; }
.btn-upi { display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 0.6rem; font-size: 0.9rem; font-weight: 700; border-radius: 10px; text-decoration: none; border: 1px solid #e2e8f0; transition: transform 0.15s, box-shadow 0.15s; }
.btn-upi:active { transform: scale(0.98); }
.btn-gpay { background: #ffffff; color: #1f1f1f; border-color: #dadce0; }
.btn-phonepe { background: #5f259f; color: #ffffff; border-color: #5f259f; }
.btn-paytm { background: #00b9f5; color: #ffffff; border-color: #00b9f5; }
.btn-generic-upi { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; border: none; grid-column: span 2; font-size: 1rem; padding: 0.9rem; }
.polling-status { display: flex; align-items: center; justify-content: center; gap: 0.6rem; font-size: 0.85rem; color: #475569; margin: 1rem 0; padding: 0.6rem; background: #f8fafc; border-radius: 8px; }
.dot-pulse { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; animation: pulse 1.5s infinite; }
@keyframes pulse { 0% { transform: scale(0.9); opacity: 0.7; } 50% { transform: scale(1.4); opacity: 1; } 100% { transform: scale(0.9); opacity: 0.7; } }
.utr-accordion { margin-top: 1.25rem; border-top: 1px solid #e2e8f0; padding-top: 1rem; font-size: 0.85rem; text-align: left; }
.utr-toggle { color: #64748b; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600; }
.utr-form { margin-top: 0.75rem; display: none; }
.utr-input { width: 100%; box-sizing: border-box; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; margin-bottom: 0.5rem; }
.utr-submit { width: 100%; padding: 0.65rem; background: #0f172a; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; }
</style>
</head>
<body>

<div class="checkout-wrap">
    <div class="checkout-card">
        <div class="secure-badge">🔒 Direct 0% Fee UPI Checkout</div>
        <h1><?= e($pkg['name']) ?></h1>
        <div class="amount-box">
            <div class="amount">₹<?= e(number_format((float)$pkg['price'], 0)) ?></div>
            <div class="amount-note">
                <?php if (!empty($pkg['duration_days'])): ?>
                    Active for <?= (int)$pkg['duration_days'] ?> days · SpiritualShaadi Plan
                <?php else: ?>
                    <?= e($pkg['tagline'] ?? 'Instant Membership Upgrade') ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- QR Code for Desktop / Scanner -->
        <div class="qr-frame">
            <img src="<?= e($qrUrl) ?>" alt="Scan to pay via UPI" id="qrImg">
        </div>
        <p style="font-size: 0.82rem; color: #64748b; margin: 0.2rem 0 0.8rem;">
            Scan with Google Pay, PhonePe, Paytm, or BHIM
        </p>

        <!-- One-Tap UPI App Buttons for Mobile -->
        <div class="upi-apps">
            <a href="<?= e($upiUri) ?>" class="btn-upi btn-generic-upi">
                ⚡ Tap to Pay with Any UPI App
            </a>
            <a href="<?= e($upiUri) ?>" class="btn-upi btn-gpay">
                Google Pay
            </a>
            <a href="<?= e($upiUri) ?>" class="btn-upi btn-phonepe">
                PhonePe
            </a>
        </div>

        <div class="polling-status" id="pollingStatus">
            <span class="dot-pulse"></span>
            <span>Waiting for payment confirmation…</span>
        </div>

        <!-- Manual 12-digit UTR Verification Accordion (Fallback) -->
        <div class="utr-accordion">
            <div class="utr-toggle" onclick="toggleUtr()">
                <span>Paid but screen didn't update?</span>
                <span id="utrArrow">▼</span>
            </div>
            <form class="utr-form" id="utrForm" method="post" action="/checkout/verify-utr">
                <?= csrf_field() ?>
                <input type="hidden" name="payment_id" value="<?= (int)$payment['id'] ?>">
                <p style="font-size:0.78rem; color:#64748b; margin:0.35rem 0 0.6rem;">
                    Enter the 12-digit UPI Transaction / UTR reference number from your payment receipt:
                </p>
                <input type="text" name="utr" class="utr-input" placeholder="e.g. 429812345678" maxlength="25" required>
                <button type="submit" class="utr-submit">Confirm &amp; Activate Plan</button>
            </form>
        </div>

        <p style="margin-top: 1.25rem; font-size: 0.75rem; color: #94a3b8;">
            Reference ID: <code><?= e($orderRef) ?></code> · <a href="/packages" style="color: #64748b; text-decoration: none;">Cancel</a>
        </p>
    </div>
</div>

<script>
const paymentId = <?= json_encode((int)$payment['id']) ?>;
const successUrl = '/checkout/success?p=' + encodeURIComponent(paymentId);
let pollTimer = null;

function checkPaymentStatus() {
    fetch('/checkout/status/' + paymentId)
        .then(r => r.json())
        .then(data => {
            if (data && data.is_paid) {
                clearInterval(pollTimer);
                const el = document.getElementById('pollingStatus');
                if (el) {
                    el.style.background = '#ecfdf5';
                    el.style.color = '#065f46';
                    el.innerHTML = '<strong>✓ Payment Received! Activating plan…</strong>';
                }
                setTimeout(() => {
                    window.location.href = successUrl;
                }, 1200);
            }
        })
        .catch(() => {});
}

// Start auto-checking payment status every 2.5 seconds
pollTimer = setInterval(checkPaymentStatus, 2500);

function toggleUtr() {
    const f = document.getElementById('utrForm');
    const a = document.getElementById('utrArrow');
    if (f.style.display === 'block') {
        f.style.display = 'none';
        a.textContent = '▼';
    } else {
        f.style.display = 'block';
        a.textContent = '▲';
    }
}
</script>

</body>
</html>
