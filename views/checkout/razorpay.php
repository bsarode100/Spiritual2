<?php /** @var array $pkg @var array $order @var string $key_id @var array $user */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pay for <?= e($pkg['name']) ?> — <?= e(setting('site_name','Spiritual Matrimony')) ?></title>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<style>
.checkout-wrap { min-height: 100vh; display: grid; place-items: center; padding: 2rem; background: var(--c-cream); }
.checkout-card { max-width: 520px; width: 100%; background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,.08); text-align: center; }
.checkout-card h1 { margin: .5rem 0; }
.amount { font-size: 2.5rem; font-weight: 700; color: var(--c-maroon); margin: 1rem 0; }
.pkg-meta { color: var(--c-ink-soft); margin-bottom: 2rem; }
.spinner { display: inline-block; width: 40px; height: 40px; border: 3px solid var(--c-cream-2); border-top-color: var(--c-saffron); border-radius: 50%; animation: spin 1s linear infinite; margin: 1rem auto; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
<div class="checkout-wrap">
    <div class="checkout-card">
        <span class="eyebrow">Secure checkout</span>
        <h1><?= e($pkg['name']) ?></h1>
        <div class="amount">₹<?= number_format((float)$pkg['price'], 0) ?></div>
        <p class="pkg-meta"><?= (int)$pkg['duration_days'] ?> days · <?= e($pkg['tagline']) ?></p>

        <div id="status">
            <div class="spinner"></div>
            <p style="color: var(--c-ink-soft);">Opening Razorpay…</p>
        </div>

        <button id="retry" class="btn btn-gold btn-lg" style="display:none;">Open Razorpay</button>
        <p style="margin-top: 1.5rem; font-size: .78rem; color: var(--c-ink-soft); line-height: 1.5;">
            By proceeding with this payment you accept our
            <a href="/page/terms-and-condition" target="_blank" rel="noopener">Terms &amp; Conditions</a>,
            <a href="/refund-policy" target="_blank" rel="noopener">Refund &amp; Cancellation Policy</a>
            and <a href="/page/privacy-policy" target="_blank" rel="noopener">Privacy Policy</a>.
        </p>
        <p style="margin-top: 1rem;"><a href="/packages" style="color: var(--c-ink-soft);">← Cancel and go back</a></p>
    </div>
</div>

<form id="verify-form" method="post" action="/checkout/verify" style="display:none;">
    <input type="hidden" name="razorpay_order_id"   id="rzp_order_id">
    <input type="hidden" name="razorpay_payment_id" id="rzp_payment_id">
    <input type="hidden" name="razorpay_signature"  id="rzp_signature">
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const options = {
    key:      <?= json_encode($key_id) ?>,
    amount:   <?= json_encode($order['amount']) ?>,
    currency: <?= json_encode($order['currency']) ?>,
    order_id: <?= json_encode($order['id']) ?>,
    name:     <?= json_encode(setting('site_name','Spiritual Matrimony')) ?>,
    description: <?= json_encode('Membership: ' . $pkg['name']) ?>,
    prefill: {
        name:    <?= json_encode($user['name'] ?? '') ?>,
        email:   <?= json_encode($user['email'] ?? '') ?>,
        contact: <?= json_encode($user['phone'] ?? '') ?>
    },
    theme: { color: '#B8860B' },
    handler: function (response) {
        document.getElementById('rzp_order_id').value   = response.razorpay_order_id;
        document.getElementById('rzp_payment_id').value = response.razorpay_payment_id;
        document.getElementById('rzp_signature').value  = response.razorpay_signature;
        document.getElementById('status').innerHTML = '<div class="spinner"></div><p style="color: var(--c-ink-soft);">Verifying payment…</p>';
        document.getElementById('verify-form').submit();
    },
    modal: {
        ondismiss: function () {
            document.getElementById('status').innerHTML =
                '<p style="color: var(--c-ink-soft);">Checkout closed without payment.</p>';
            document.getElementById('retry').style.display = 'inline-block';
        }
    }
};
function openCheckout() {
    const rzp = new Razorpay(options);
    rzp.on('payment.failed', function (resp) {
        document.getElementById('status').innerHTML =
            '<p style="color: #b3261e;">Payment failed: ' + (resp.error && resp.error.description ? resp.error.description : 'unknown error') + '</p>';
        document.getElementById('retry').style.display = 'inline-block';
    });
    rzp.open();
}
document.getElementById('retry').addEventListener('click', openCheckout);
window.addEventListener('load', () => setTimeout(openCheckout, 250));
</script>
</body>
</html>
