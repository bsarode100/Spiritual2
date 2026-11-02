<?php /** @var array $payment @var array $user */
$utr = $payment['gateway_payment_id'] ?? '';
$amount = number_format((float)$payment['amount'], 2);
$packageName = $payment['package_name'] ?? 'Membership Plan';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Payment Under Verification — SpiritualShaadi</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    background: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    color: #0f172a;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 1rem;
}
.card {
    background: #ffffff;
    max-width: 480px;
    width: 100%;
    border-radius: 20px;
    padding: 2.25rem 1.75rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
    text-align: center;
}
.icon-circle {
    width: 68px;
    height: 68px;
    background: #fef3c7;
    border: 3px solid #f59e0b;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1.25rem;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 12px rgba(245, 158, 11, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}
h1 {
    font-size: 1.4rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 0.5rem;
}
.subtitle {
    font-size: 0.92rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 1.5rem;
}
.details-box {
    background: #f1f5f9;
    border-radius: 14px;
    padding: 1.15rem 1.25rem;
    margin-bottom: 1.5rem;
    text-align: left;
    font-size: 0.9rem;
}
.details-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.45rem 0;
    border-bottom: 1px dashed #cbd5e1;
}
.details-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.details-label {
    color: #64748b;
    font-weight: 500;
}
.details-val {
    font-weight: 700;
    color: #0f172a;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #fef3c7;
    color: #92400e;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    border: 1px solid #fde68a;
}
.dot {
    width: 8px;
    height: 8px;
    background: #f59e0b;
    border-radius: 50%;
    animation: blink 1.2s infinite;
}
@keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
.timeline {
    margin: 1.5rem 0;
    text-align: left;
    font-size: 0.82rem;
    color: #475569;
}
.timeline-item {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    margin-bottom: 0.5rem;
}
.btn-primary {
    display: block;
    width: 100%;
    background: #4338ca;
    color: #ffffff;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0.85rem;
    border-radius: 12px;
    text-decoration: none;
    text-align: center;
    margin-bottom: 0.75rem;
    transition: background 0.15s;
}
.btn-primary:hover { background: #3730a3; }
.btn-secondary {
    display: block;
    width: 100%;
    background: transparent;
    color: #64748b;
    font-weight: 600;
    font-size: 0.88rem;
    padding: 0.65rem;
    border-radius: 12px;
    text-decoration: none;
    text-align: center;
}
</style>
</head>
<body>

<div class="card">
    <div class="icon-circle">⏳</div>
    <h1>Payment Submitted</h1>
    <p class="subtitle">
        We have received your UTR number. Your transaction is currently being verified against our bank statement.
    </p>

    <div class="details-box">
        <div class="details-row">
            <span class="details-label">Plan</span>
            <span class="details-val"><?= e($packageName) ?></span>
        </div>
        <div class="details-row">
            <span class="details-label">Amount Paid</span>
            <span class="details-val">₹<?= e($amount) ?></span>
        </div>
        <?php if (!empty($utr)): ?>
            <div class="details-row">
                <span class="details-label">Submitted UTR</span>
                <span class="details-val"><code><?= e($utr) ?></code></span>
            </div>
        <?php endif; ?>
        <div class="details-row">
            <span class="details-label">Status</span>
            <span class="status-pill"><span class="dot"></span> Pending Verification</span>
        </div>
    </div>

    <div class="timeline">
        <div class="timeline-item">✓ Order created successfully</div>
        <div class="timeline-item">✓ UTR reference recorded</div>
        <div class="timeline-item">⏳ Bank settlement verification (usually 5–15 mins)</div>
        <div class="timeline-item" style="color:#94a3b8;">⚪ Membership plan activation</div>
    </div>

    <a href="/dashboard" class="btn-primary">Return to Dashboard</a>
    <a href="/billing" class="btn-secondary">View in Billing History →</a>
</div>

<script>
const paymentId = <?= json_encode((int)$payment['id']) ?>;
const successUrl = '/checkout/success?p=' + encodeURIComponent(paymentId);

// Check if admin or webhook approved the payment in background
setInterval(() => {
    fetch('/checkout/status/' + paymentId)
        .then(r => r.json())
        .then(data => {
            if (data && data.is_paid) {
                window.location.href = successUrl;
            }
        })
        .catch(() => {});
}, 3000);
</script>

</body>
</html>
