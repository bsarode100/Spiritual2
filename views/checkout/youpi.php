<?php /** @var array $pkg @var array $payment @var string $portal_url @var array $user @var string $kind */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pay for <?= e($pkg['name']) ?> — <?= e(setting('site_name','SpiritualShaadi')) ?></title>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<style>
.checkout-wrap { min-height: 100vh; display: grid; place-items: center; padding: 2rem; background: var(--c-cream); }
.checkout-card { max-width: 520px; width: 100%; background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,.08); text-align: center; }
.checkout-card h1 { margin: .5rem 0; font-family: var(--f-serif, serif); color: var(--c-maroon); }
.amount { font-size: 2.5rem; font-weight: 700; color: var(--c-maroon); margin: 1rem 0; }
.pkg-meta { color: var(--c-ink-soft); margin-bottom: 1.5rem; font-size: 0.95rem; }
.spinner { display: inline-block; width: 40px; height: 40px; border: 3px solid var(--c-cream-2, #f5ecd7); border-top-color: var(--c-saffron, #D99420); border-radius: 50%; animation: spin 1s linear infinite; margin: 1rem auto; }
@keyframes spin { to { transform: rotate(360deg); } }
.secure-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.85rem; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 0.8rem; font-weight: 600; border-radius: 20px; margin-bottom: 1rem; }
.btn-pay { display: inline-block; width: 100%; padding: 0.9rem 1.5rem; font-size: 1.1rem; font-weight: 600; color: #fff; background: linear-gradient(135deg, var(--c-saffron, #D99420) 0%, #b87508 100%); border: none; border-radius: 8px; cursor: pointer; text-decoration: none; transition: opacity 0.2s; }
.btn-pay:hover { opacity: 0.95; color: #fff; }
</style>
<!-- Load YouPI Universal Checkout SDK -->
<script src="<?= e(rtrim($portal_url, '/')) ?>/sdk/youpi.js"></script>
</head>
<body>
<div class="checkout-wrap">
    <div class="checkout-card">
        <div class="secure-badge">🔒 Direct UPI Bank Checkout</div>
        <h1><?= e($pkg['name']) ?></h1>
        <div class="amount">₹<?= number_format((float)$pkg['price'], 0) ?></div>
        <?php if (!empty($pkg['duration_days'])): ?>
            <p class="pkg-meta"><?= (int)$pkg['duration_days'] ?> days · <?= e($pkg['tagline'] ?? 'Premium Spiritual Matchmaking') ?></p>
        <?php else: ?>
            <p class="pkg-meta"><?= e($pkg['tagline'] ?? 'SpiritualShaadi Service') ?></p>
        <?php endif; ?>

        <div id="status">
            <div class="spinner"></div>
            <p style="color: var(--c-ink-soft);">Opening Secure UPI Payment…</p>
        </div>

        <button id="retry" class="btn-pay" style="display:none; margin-top: 1rem;">Open UPI Checkout</button>

        <p style="margin-top: 1.5rem; font-size: .78rem; color: var(--c-ink-soft); line-height: 1.5;">
            Zero gateway fees · Direct Bank-to-Bank UPI transfer.<br/>
            By proceeding you accept our
            <a href="/page/terms" target="_blank" rel="noopener">Terms &amp; Conditions</a>,
            <a href="/page/refund-policy" target="_blank" rel="noopener">Refund Policy</a>
            and <a href="/page/privacy" target="_blank" rel="noopener">Privacy Policy</a>.
        </p>
        <p style="margin-top: 1rem;"><a href="/packages" style="color: var(--c-ink-soft); text-decoration: none;">← Cancel and go back</a></p>
    </div>
</div>

<script>
const paymentId = <?= json_encode((int)$payment['id']) ?>;
const successUrl = '/checkout/success?p=' + encodeURIComponent(paymentId);

function openYoupi() {
    if (typeof Youpi === 'undefined') {
        setTimeout(openYoupi, 250);
        return;
    }

    try {
        Youpi.pay({
            order_id: String(paymentId),
            amount: <?= json_encode((float)$pkg['price']) ?>,
            currency: 'INR',
            name: <?= json_encode(setting('site_name', 'SpiritualShaadi')) ?>,
            description: <?= json_encode($pkg['name'] . ' Membership') ?>,
            customer: {
                name: <?= json_encode($user['name'] ?? 'Seeker') ?>,
                email: <?= json_encode($user['email'] ?? '') ?>,
                phone: <?= json_encode($user['phone'] ?? '') ?>
            },
            metadata: {
                payment_id: paymentId,
                user_id: <?= json_encode($user['id'] ?? 0) ?>,
                purchase_type: <?= json_encode($kind) ?>
            },
            handler: function(response) {
                document.getElementById('status').innerHTML =
                    '<div class="spinner"></div><p style="color: #059669; font-weight:600;">Payment confirmed by bank! Activating membership…</p>';
                window.location.href = successUrl;
            },
            onFailed: function(response) {
                document.getElementById('status').innerHTML =
                    '<p style="color: #b3261e; font-weight:600;">Payment not completed: ' + (response.reason || 'Expired or cancelled') + '</p>';
                document.getElementById('retry').style.display = 'inline-block';
            },
            onClose: function() {
                document.getElementById('status').innerHTML =
                    '<p style="color: var(--c-ink-soft);">Checkout closed. Click below to pay whenever you are ready.</p>';
                document.getElementById('retry').style.display = 'inline-block';
            }
        });
    } catch (e) {
        console.error('YouPI launch error:', e);
        document.getElementById('status').innerHTML =
            '<p style="color: #b3261e;">Could not open payment window. Please click below to retry.</p>';
        document.getElementById('retry').style.display = 'inline-block';
    }
}

document.getElementById('retry').addEventListener('click', openYoupi);
window.addEventListener('load', () => setTimeout(openYoupi, 200));
</script>
</body>
</html>
