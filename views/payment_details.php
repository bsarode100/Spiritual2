<?php /** @var array $details */
$has_upi  = $details['upi_id'] !== '' || $details['upi_qr_url'] !== '';
$has_bank = $details['account_number'] !== '' || $details['ifsc'] !== '' || $details['bank_name'] !== '';
$has_help = $details['contact_phone'] !== '' || $details['contact_email'] !== '' || $details['instructions'] !== '';
$anything = $has_upi || $has_bank || $has_help;
?>
<section class="section-tight" style="padding: 5rem 0 3rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container text-center">
        <span class="eyebrow">Payments</span>
        <h1>Payment <em style="color: var(--c-saffron); font-family: var(--f-display);">Details</em></h1>
        <p style="font-size: 1.1rem; color: var(--c-ink-soft); max-width: 640px; margin: 0 auto;">
            Pay securely via UPI or bank transfer and activate your membership. Share the transaction reference with our team for quick activation.
        </p>
    </div>
</section>

<section class="section"><div class="container">
    <?php if (!$anything): ?>
        <div class="admin-card text-center" style="max-width: 640px; margin: 0 auto;">
            <h3>Payment details coming soon</h3>
            <p style="color: var(--c-ink-soft);">We haven't published our payment details yet. Please <a href="/contact">contact us</a> and we'll share them directly.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; align-items: start;">

            <?php if ($has_upi): ?>
            <div class="admin-card">
                <h3 style="color: var(--c-maroon);">📱 UPI</h3>
                <?php if ($details['payee_name'] !== ''): ?>
                    <p style="color: var(--c-ink-soft); margin-bottom: .25rem;">Payee</p>
                    <p style="font-weight: 600; margin-bottom: 1rem;"><?= e($details['payee_name']) ?></p>
                <?php endif; ?>
                <?php if ($details['upi_id'] !== ''): ?>
                    <p style="color: var(--c-ink-soft); margin-bottom: .25rem;">UPI ID</p>
                    <p style="font-weight: 600; margin-bottom: 1rem;"><code style="background: var(--c-cream-2); padding: .25rem .5rem; border-radius: 6px;"><?= e($details['upi_id']) ?></code></p>
                <?php endif; ?>
                <?php if ($details['upi_qr_url'] !== ''): ?>
                    <div style="text-align: center; margin-top: 1rem;">
                        <img src="<?= e($details['upi_qr_url']) ?>" alt="Scan to pay via UPI" style="max-width: 240px; width: 100%; border-radius: 12px; border: 1px solid var(--c-cream-2);">
                        <p style="color: var(--c-muted); font-size: .9rem; margin-top: .5rem;">Scan with any UPI app — GPay, PhonePe, Paytm, BHIM.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($has_bank): ?>
            <div class="admin-card">
                <h3 style="color: var(--c-maroon);">🏦 Bank Transfer (NEFT / IMPS / RTGS)</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <?php
                    $rows = [
                        'Bank'           => $details['bank_name'],
                        'Account Name'   => $details['account_name'] !== '' ? $details['account_name'] : $details['payee_name'],
                        'Account Number' => $details['account_number'],
                        'IFSC'           => $details['ifsc'],
                        'Branch'         => $details['branch'],
                    ];
                    foreach ($rows as $label => $val):
                        if ($val === '' || $val === null) continue;
                    ?>
                        <tr>
                            <td style="padding: .55rem 0; color: var(--c-ink-soft); width: 45%;"><?= e($label) ?></td>
                            <td style="padding: .55rem 0; font-weight: 600;"><?php
                                if (in_array($label, ['Account Number','IFSC'], true)) {
                                    echo '<code style="background: var(--c-cream-2); padding: .15rem .4rem; border-radius: 6px;">' . e($val) . '</code>';
                                } else {
                                    echo e($val);
                                }
                            ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif; ?>

            <?php if ($has_help): ?>
            <div class="admin-card">
                <h3 style="color: var(--c-maroon);">🙏 After You Pay</h3>
                <?php if ($details['instructions'] !== ''): ?>
                    <p style="color: var(--c-ink-soft); white-space: pre-line;"><?= e($details['instructions']) ?></p>
                <?php endif; ?>
                <?php if ($details['contact_phone'] !== '' || $details['contact_email'] !== ''): ?>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--c-cream-2);">
                    <?php if ($details['contact_phone'] !== ''): ?>
                        <p style="margin-bottom: .5rem;">📞 <a href="tel:<?= e(preg_replace('/[^+0-9]/', '', $details['contact_phone'])) ?>"><?= e($details['contact_phone']) ?></a></p>
                    <?php endif; ?>
                    <?php if ($details['contact_email'] !== ''): ?>
                        <p style="margin-bottom: 0;">📧 <a href="mailto:<?= e($details['contact_email']) ?>"><?= e($details['contact_email']) ?></a></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>

        <div class="text-center" style="margin-top: 3rem;">
            <a href="/packages" class="btn btn-gold">← Back to Packages</a>
            <a href="/contact" class="btn btn-ghost">Need help? Contact us</a>
        </div>
    <?php endif; ?>
</div></section>
