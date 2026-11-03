<?php /** @var ?array $pmt */ $isVerification = ($pmt['purchase_type'] ?? '') === 'verification'; ?>
<section class="section-tight" style="padding: 2.5rem 1rem 3rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container-sm text-center" style="max-width: 520px; margin: 0 auto; padding: 0;">
        <div style="width: 80px; height: 80px; background: #ECFDF5; border: 3px solid #10B981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1.25rem; box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.12);">
            🪔
        </div>
        <span class="eyebrow" style="color: #047857; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 0.3rem 0.8rem; border-radius: 9999px; font-weight: 700; font-size: 0.82rem; display: inline-block; margin-bottom: 0.75rem;">
            ✓ Payment Successful &amp; Activated
        </span>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--c-ink); margin-bottom: 0.5rem; line-height: 1.25;">
            Welcome to <em style="color: var(--c-saffron); font-family: var(--f-display); font-style: normal;"><?= e($pmt['package_name'] ?? 'your plan') ?></em>
        </h1>
        <?php if ($pmt): ?>
            <p style="font-size: 0.98rem; color: var(--c-ink-soft); line-height: 1.5; margin: 0.75rem auto 1.5rem;">
                <?php if ($isVerification): ?>
                    One more step — submit your government ID<?= str_contains($pmt['package_name'] ?? '', 'Selfie') ? ' and capture a live selfie' : '' ?> so our team can review and award your verified badge.
                <?php else: ?>
                    Your membership is now active for the next <strong><?= (int)$pmt['duration_days'] ?> days</strong>. A confirmation has been sent to your registered email.
                <?php endif; ?>
            </p>
            <div class="admin-card" style="max-width: 440px; margin: 0 auto 1.75rem; text-align: left; background: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.25rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
                <div style="font-size: 0.78rem; font-weight: 700; color: #8B1E3F; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.65rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                    Official Receipt Details
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.88rem;">
                    <span style="color: #64748B;">Amount Paid:</span>
                    <strong style="color: #047857; font-size: 0.95rem;">₹<?= number_format((float)$pmt['amount'], 2) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.88rem;">
                    <span style="color: #64748B;">Transaction Ref:</span>
                    <code style="font-size: 0.82rem; font-family: monospace; font-weight: 700; color: #1E293B;"><?= e($pmt['gateway_payment_id']) ?></code>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.88rem;">
                    <span style="color: #64748B;">Order Number:</span>
                    <code style="font-size: 0.82rem; font-family: monospace; color: #64748B;"><?= e($pmt['gateway_order_id']) ?></code>
                </div>
            </div>
        <?php endif; ?>

        <div style="display: flex; flex-direction: column; gap: 0.65rem; max-width: 440px; margin: 0 auto;">
            <?php if ($isVerification): ?>
                <a href="/verification" class="btn btn-gold btn-lg" style="min-height: 48px; display: flex; align-items: center; justify-content: center; font-weight: 700;">Submit Documents Now</a>
                <a href="/dashboard" class="btn btn-ghost btn-lg" style="min-height: 44px; display: flex; align-items: center; justify-content: center;">Go to my Dashboard</a>
            <?php else: ?>
                <a href="/browse" class="btn btn-gold btn-lg" style="min-height: 48px; display: flex; align-items: center; justify-content: center; font-weight: 700;">Start Browsing Matches →</a>
                <a href="/dashboard" class="btn btn-ghost btn-lg" style="min-height: 44px; display: flex; align-items: center; justify-content: center;">Go to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</section>
