<?php /** @var array $pkg @var array $payment @var string $portal_url @var array $user @var string $kind */
$rawUpi = trim((string)(setting('payment_upi_id') ?: setting('youpi_vpa') ?: ''));

// Automatically extract parameters if full URI or query string was entered
$extractedVpa = '';
$extractedMcc = '';
$extractedPn  = '';

if (preg_match('/[?&]pa=([^&]+)/i', $rawUpi, $m)) {
    $extractedVpa = urldecode($m[1]);
}
if (preg_match('/[?&]mc=([^&]+)/i', $rawUpi, $m)) {
    $extractedMcc = urldecode($m[1]);
}
if (preg_match('/[?&]pn=([^&]+)/i', $rawUpi, $m)) {
    $extractedPn = urldecode($m[1]);
}

if (!empty($extractedVpa)) {
    $upiId = $extractedVpa;
} else {
    // Strip accidental prefixes
    $upiId = preg_replace('/^(upi:\/\/pay\?pa=|pa=|upi:|vpa:|id:)[\s\/]*/i', '', $rawUpi);
    $upiId = explode('?', $upiId)[0];
    $upiId = explode('&', $upiId)[0];
    $upiId = preg_replace('/\s+/', '', $upiId);
}

$payeeName = $extractedPn ?: trim((string)(setting('payment_payee_name') ?: setting('site_name', 'SpiritualShaadi')));
$cleanPayee = preg_replace('/[^a-zA-Z0-9 ]/', '', substr($payeeName, 0, 25));
if (empty($cleanPayee)) {
    $cleanPayee = 'SpiritualShaadi';
}

$isConfigured = !empty($upiId) && str_contains($upiId, '@') && $upiId !== 'spiritualmatrimony@upi';

$amount = number_format((float)$pkg['price'], 2, '.', '');
$orderRef = $payment['gateway_order_id'] ?? ('SS' . $payment['id']);

$encodedPayee = rawurlencode($cleanPayee);
$uploadedQrUrl = trim((string)setting('payment_upi_qr_url', ''));

// Standard NPCI Dynamic QR with exact pre-filled amount
$baseUpiQuery  = "pa={$upiId}&pn={$encodedPayee}&am={$amount}&cu=INR";
$upiUriQr      = "upi://pay?{$baseUpiQuery}";

// Generate reliable dynamic QR codes with exact amount pre-filled
$generatedQrUrl = "https://quickchart.io/qr?size=260&margin=2&text=" . rawurlencode($upiUriQr);
$fallbackQrUrl  = "https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=8&data=" . rawurlencode($upiUriQr);

$displayQrUrl   = $generatedQrUrl;
$alreadySubmittedUtr = !empty($payment['gateway_payment_id']) ? $payment['gateway_payment_id'] : '';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Pay ₹<?= e(number_format((float)$pkg['price'], 0)) ?> — <?= e($cleanPayee) ?></title>
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

/* Config Warning */
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

/* QR Code Display Wrapper */
.qr-wrapper {
    display: block;
    margin-top: 0.5rem;
    padding: 1rem 0.85rem;
    background: #ffffff;
    border-radius: 16px;
    border: 2px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.qr-wrapper img {
    width: 220px;
    height: 220px;
    border-radius: 12px;
    display: block;
    margin: 0 auto;
    border: 1px solid #e2e8f0;
}
.btn-save-qr {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #047857;
    color: #ffffff;
    font-size: 0.88rem;
    font-weight: 700;
    padding: 0.65rem 1.15rem;
    border-radius: 9999px;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(4,120,87,0.25);
    transition: transform 0.1s, opacity 0.15s;
}
.btn-save-qr:active { transform: scale(0.97); }
.btn-save-qr:hover { opacity: 0.95; }

.qr-mobile-guide {
    margin-top: 0.85rem;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 10px;
    padding: 0.75rem 0.85rem;
    font-size: 0.78rem;
    color: #065f46;
    line-height: 1.5;
    text-align: left;
}

/* Copy UPI ID Box */
.copy-upi-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.65rem 0.85rem;
    margin: 0.85rem 0 0;
    text-align: left;
}
.copy-upi-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.btn-copy {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 0.35rem 0.65rem;
    font-size: 0.78rem;
    font-weight: 700;
    color: #0f172a;
    cursor: pointer;
}
.btn-copy:active { background: #e2e8f0; }

/* Step 2: UTR Submission Form */
.utr-card {
    margin-top: 1.25rem;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 14px;
    padding: 1rem;
    text-align: left;
}
.utr-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}
.utr-card-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0f172a;
}
.utr-field {
    width: 100%;
    padding: 0.75rem 0.85rem;
    border: 1.5px solid #94a3b8;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    background: #ffffff;
    letter-spacing: 0.05em;
    margin-top: 0.4rem;
    margin-bottom: 0.65rem;
    outline: none;
    transition: border-color 0.15s;
}
.utr-field:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
}
.utr-submit-btn {
    width: 100%;
    padding: 0.75rem;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 0.92rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(15,23,42,0.2);
    transition: transform 0.1s, opacity 0.15s;
}
.utr-submit-btn:active { transform: scale(0.98); opacity: 0.92; }

/* Polling Live Status */
.status-pill {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1rem;
    padding: 0.6rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
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
                Your receiver UPI ID is not configured. Please open <a href="/admin/youpi" style="color:#991b1b; font-weight:700;">Admin → ⚡ YouPI Gateway</a> and paste your active UPI ID.
            </div>
        <?php endif; ?>

        <?php if (!empty($alreadySubmittedUtr)): ?>
            <div style="background: #fffbeb; border: 2px solid #f59e0b; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; color: #92400e; text-align: left;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                    <strong style="display: flex; align-items: center; gap: 0.35rem;">
                        <span>⏳</span> UTR Submitted — Under Verification
                    </strong>
                    <span style="background: #fef3c7; color: #92400e; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 9999px; border: 1px solid #fde68a;">Pending</span>
                </div>
                <p style="margin: 0.4rem 0 0; font-size: 0.85rem; line-height: 1.45;">
                    Your UPI Reference Number (UTR: <code><?= e($alreadySubmittedUtr) ?></code>) was received. We are verifying bank credit. Your plan will activate within 5–15 minutes.
                </p>
                <div style="margin-top: 0.6rem; font-size: 0.82rem;">
                    <a href="/checkout/pending?p=<?= (int)$payment['id'] ?>" style="color: #78350f; font-weight: 700; text-decoration: underline;">View Status Page →</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- STEP 1: 100% Reliable Dynamic QR Code (Works with All UPI Apps) -->
        <div class="qr-wrapper" id="qrWrapper">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="font-size: 0.85rem; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.03em;">
                    ⚡ Step 1: Scan &amp; Pay ₹<?= e(number_format((float)$pkg['price'], 0)) ?>
                </span>
                <span style="font-size: 0.72rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.2rem 0.55rem; border-radius: 9999px;">
                    ✓ 100% Accepted
                </span>
            </div>

            <img src="<?= e($displayQrUrl) ?>" 
                 onerror="if(this.src!=='<?= e($fallbackQrUrl) ?>'){this.src='<?= e($fallbackQrUrl) ?>';}" 
                 alt="UPI QR Code" id="qrImage">
            
            <p style="font-size:0.95rem; font-weight:800; color:#0f172a; margin-top:0.65rem;">
                Pay ₹<?= e(number_format((float)$pkg['price'], 0)) ?> to <?= e($cleanPayee) ?>
            </p>
            <p style="font-size:0.75rem; color:#64748b; margin-top:0.2rem;">
                UPI ID: <code><?= e($upiId) ?></code>
            </p>

            <!-- Save / Download QR for Single Phone Mobile Users -->
            <div style="margin-top: 0.85rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                <a href="<?= e($displayQrUrl) ?>" download="SpiritualShaadi-QR-<?= e(number_format((float)$pkg['price'], 0)) ?>.png" target="_blank" class="btn-save-qr">
                    <span>📥</span> Save QR Image to Gallery
                </a>
            </div>

            <!-- Single Phone Instructions -->
            <div class="qr-mobile-guide">
                <strong>📱 Paying from this phone? (Single-device steps):</strong>
                <ol style="margin: 0.35rem 0 0 1.15rem; padding: 0; line-height: 1.45;">
                    <li>Tap <strong>Save QR Image</strong> above (or take a screenshot).</li>
                    <li>Open <strong>PhonePe, Google Pay, or Paytm</strong>.</li>
                    <li>Tap <strong>Scan 📷</strong>, then tap the <strong>Gallery 🖼️</strong> icon to select this QR code.</li>
                    <li>The amount <strong>₹<?= e(number_format((float)$pkg['price'], 0)) ?></strong> is auto-filled. Enter your UPI PIN to pay!</li>
                </ol>
            </div>

            <?php if (!empty($uploadedQrUrl)): ?>
                <div style="margin-top:0.55rem;">
                    <a href="javascript:void(0)" onclick="toggleQrType()" id="qrSwitchLink" style="font-size:0.75rem; color:#4338ca; text-decoration:underline;">Switch to store / merchant QR</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Copy UPI ID Feature -->
        <?php if (!empty($upiId)): ?>
            <div class="copy-upi-box">
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:0.35rem;">Or transfer directly to this UPI ID:</div>
                <div class="copy-upi-row">
                    <code id="upiIdText" style="font-weight:700; color:#0f172a; font-size:0.92rem;"><?= e($upiId) ?></code>
                    <button type="button" class="btn-copy" id="copyBtn" onclick="copyUpi()">📋 Copy</button>
                </div>
            </div>
        <?php endif; ?>

        <!-- STEP 2: Submit UTR for Instant Activation -->
        <div class="utr-card">
            <div class="utr-card-header">
                <span class="utr-card-title">📝 Step 2: Submit 12-Digit UTR</span>
                <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Fast Activation</span>
            </div>
            <p style="font-size: 0.76rem; color: #64748b; line-height: 1.4;">
                After payment, copy the 12-digit <strong>UPI Reference / UTR Number</strong> from your PhonePe/GPay receipt and paste it below:
            </p>
            <form method="post" action="/checkout/verify-utr">
                <?= csrf_field() ?>
                <input type="hidden" name="payment_id" value="<?= (int)$payment['id'] ?>">
                <input type="text" name="utr" class="utr-field" placeholder="e.g. 429812345678" value="<?= e($alreadySubmittedUtr) ?>" maxlength="25" pattern="[0-9A-Za-z]{6,25}" required>
                <button type="submit" class="utr-submit-btn">
                    <?= !empty($alreadySubmittedUtr) ? 'Update Submitted UTR' : 'Submit UTR & Activate Plan' ?>
                </button>
            </form>
        </div>

        <!-- Live Auto-Confirmation Status -->
        <div class="status-pill" id="statusPill">
            <span class="pulse-dot"></span>
            <span id="statusText">Waiting for payment confirmation…</span>
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

function copyUpi() {
    const text = document.getElementById('upiIdText')?.textContent;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyBtn');
        if (btn) {
            btn.textContent = '✓ Copied!';
            btn.style.color = '#059669';
            setTimeout(() => {
                btn.textContent = '📋 Copy';
                btn.style.color = '#0f172a';
            }, 2500);
        }
    }).catch(() => {
        alert('UPI ID: ' + text);
    });
}

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

let showingOfficial = false;
const officialQrUrl = <?= json_encode($uploadedQrUrl) ?>;
const dynamicQrUrl  = <?= json_encode($generatedQrUrl) ?>;

function toggleQrType() {
    const img = document.getElementById('qrImage');
    const link = document.getElementById('qrSwitchLink');
    if (!img || !link) return;
    if (showingOfficial) {
        img.src = dynamicQrUrl;
        link.textContent = 'Switch to store / merchant QR';
        showingOfficial = false;
    } else {
        img.src = officialQrUrl;
        link.textContent = 'Switch back to auto-fill ₹<?= e(number_format((float)$pkg['price'], 0)) ?> QR';
        showingOfficial = true;
    }
}
</script>

</body>
</html>