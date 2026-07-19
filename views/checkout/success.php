<?php /** @var ?array $pmt */ $isVerification = ($pmt['purchase_type'] ?? '') === 'verification'; ?>
<section class="section-tight" style="padding: 5rem 0 3rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container-sm text-center">
        <div style="font-size: 4rem;">🪔</div>
        <span class="eyebrow">Payment successful</span>
        <h1>Welcome to <em style="color: var(--c-saffron); font-family: var(--f-display);"><?= e($pmt['package_name'] ?? 'your plan') ?></em></h1>
        <?php if ($pmt): ?>
            <p style="font-size: 1.1rem; color: var(--c-ink-soft); max-width: 560px; margin: 1rem auto;">
                <?php if ($isVerification): ?>
                    One more step — submit your government ID<?= str_contains($pmt['package_name'] ?? '', 'Selfie') ? ' and capture a live selfie' : '' ?> so our team can review and award your verified badge.
                <?php else: ?>
                    Your membership is active for the next <strong><?= (int)$pmt['duration_days'] ?> days</strong>. A confirmation has been sent to your registered email.
                <?php endif; ?>
            </p>
            <div class="admin-card" style="max-width: 480px; margin: 2rem auto; text-align: left;">
                <p><strong>Amount paid:</strong> ₹<?= number_format((float)$pmt['amount'], 2) ?></p>
                <p><strong>Payment ID:</strong> <code style="font-size: .85rem;"><?= e($pmt['gateway_payment_id']) ?></code></p>
                <p style="margin: 0;"><strong>Reference:</strong> <code style="font-size: .85rem;"><?= e($pmt['gateway_order_id']) ?></code></p>
            </div>
        <?php endif; ?>
        <?php if ($isVerification): ?>
            <a href="/verification" class="btn btn-gold btn-lg">Submit Documents Now</a>
            <a href="/dashboard" class="btn btn-ghost btn-lg">Go to my Dashboard</a>
        <?php else: ?>
            <a href="/dashboard" class="btn btn-gold btn-lg">Go to my Dashboard</a>
            <a href="/browse" class="btn btn-ghost btn-lg">Start browsing matches</a>
        <?php endif; ?>
    </div>
</section>
