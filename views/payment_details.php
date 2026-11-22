<?php /** @var array $details */
$has_upi  = ($details['upi_id'] ?? '') !== '' || ($details['upi_qr_url'] ?? '') !== '';
$has_bank = ($details['account_number'] ?? '') !== '' || ($details['ifsc'] ?? '') !== '' || ($details['bank_name'] ?? '') !== '';
$has_help = ($details['contact_phone'] ?? '') !== '' || ($details['contact_email'] ?? '') !== '' || ($details['instructions'] ?? '') !== '';
$anything = $has_upi || $has_bank || $has_help;

$contactPhone = $details['contact_phone'] !== '' ? $details['contact_phone'] : setting('contact_phone', '+91 7744922099');
$contactEmail = $details['contact_email'] !== '' ? $details['contact_email'] : setting('contact_email', 'support@spiritualshaadi.com');
$waPhone = preg_replace('/[^+0-9]/', '', $contactPhone);
$cleanWaPhone = ltrim($waPhone, '+');
?>
<section class="section-tight" style="padding: 5rem 0 2.5rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container text-center">
        <span class="eyebrow" style="display: inline-flex; align-items: center; gap: .45rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Secure Transactions &amp; Instant Activation
        </span>
        <h1 style="margin: .8rem 0 1rem; font-size: clamp(2.2rem, 5vw, 3.2rem);">
            Payment <em style="color: var(--c-saffron); font-family: var(--f-display); font-style: italic;">Details</em>
        </h1>
        <p style="font-size: 1.15rem; color: var(--c-ink-soft); max-width: 680px; margin: 0 auto; line-height: 1.7;">
            Pay securely via instant UPI QR or direct bank transfer (NEFT / IMPS). Share your transaction reference with our team for priority activation within minutes.
        </p>
    </div>
</section>

<section class="section" style="padding-top: 0.5rem;">
    <div class="container">
        <?php if (!$anything): ?>
            <div class="payment-card text-center" style="max-width: 680px; margin: 0 auto; padding: 3rem 2rem;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🙏</div>
                <h3 style="font-family: var(--f-display); color: var(--c-ink);">Payment Details Coming Soon</h3>
                <p style="color: var(--c-ink-soft); line-height: 1.6; margin-bottom: 1.5rem;">
                    We are currently setting up direct automated payment gateways. Please contact our support team and we will share payment instructions directly.
                </p>
                <a href="/contact" class="btn btn-primary">Contact Support</a>
            </div>
        <?php else: ?>
            <div class="payment-layout-grid">

                <!-- Left Column: Instant UPI Payment & QR Code -->
                <?php if ($has_upi): ?>
                <div class="payment-card">
                    <div class="payment-card-header">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--c-blush-soft); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            📱
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.25rem;">Instant UPI Payment</h3>
                            <span style="font-size: .8rem; color: #2F6F4F; font-weight: 600;">✓ Verified Business Channel</span>
                        </div>
                    </div>

                    <?php if (!empty($details['payee_name'])): ?>
                        <div style="margin-bottom: 1rem;">
                            <span style="font-size: .82rem; text-transform: uppercase; letter-spacing: .04em; color: var(--c-muted); font-weight: 600;">Account / Payee Name</span>
                            <div style="font-weight: 700; font-size: 1.05rem; color: var(--c-ink); margin-top: .15rem;"><?= e($details['payee_name']) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($details['upi_id'])): ?>
                        <div>
                            <span style="font-size: .82rem; text-transform: uppercase; letter-spacing: .04em; color: var(--c-muted); font-weight: 600;">Official UPI ID</span>
                            <div class="upi-id-box">
                                <code><?= e($details['upi_id']) ?></code>
                                <button type="button" class="copy-btn" onclick="copyToClipboard('<?= e($details['upi_id']) ?>', this)" title="Copy UPI ID to clipboard">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    Copy
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php 
                    $qrSrc = $details['upi_qr_url'] ?? '';
                    if (empty($qrSrc) && !empty($details['upi_id'])) {
                        $qrCleanPayee = rawurlencode(preg_replace('/[^a-zA-Z0-9 ]/', '', substr($details['payee_name'] ?: 'SpiritualShaadi', 0, 25)));
                        $qrSrc = "https://quickchart.io/qr?size=260&margin=2&text=" . rawurlencode("upi://pay?pa=" . rawurlencode($details['upi_id']) . "&pn=" . $qrCleanPayee . "&cu=INR");
                    }
                    ?>
                    <?php if (!empty($qrSrc)): ?>
                        <div class="upi-qr-frame">
                            <img src="<?= e($qrSrc) ?>" alt="Scan to pay via UPI" style="max-width: 220px; width: 100%; border-radius: 10px; display: block; margin: 0 auto;">
                            <div style="font-size: .85rem; font-weight: 600; color: var(--c-ink); margin-top: .85rem;">Scan with Any UPI App</div>
                            <div style="font-size: .8rem; color: var(--c-muted); margin-top: .2rem;">Google Pay · PhonePe · Paytm · BHIM · Cred</div>
                        </div>
                    <?php endif; ?>

                    <div style="background: var(--c-cream-2); border-radius: var(--r-sm); padding: .85rem 1rem; font-size: .84rem; color: var(--c-ink-soft); line-height: 1.5;">
                        💡 <strong>Quick Tip:</strong> On mobile, you can tap copy above and paste directly into Google Pay, PhonePe, or BHIM.
                    </div>
                </div>
                <?php endif; ?>

                <!-- Right Column: Bank Transfer & 3-Step Fast Activation -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    
                    <?php if ($has_bank): ?>
                    <div class="payment-card">
                        <div class="payment-card-header">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--c-gold-light); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                🏦
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 1.25rem;">Direct Bank Transfer (NEFT / IMPS / RTGS)</h3>
                                <span style="font-size: .8rem; color: var(--c-muted);">Direct account-to-account settlement</span>
                            </div>
                        </div>

                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: .95rem;">
                                <?php
                                $rows = [
                                    'Bank Name'      => ['val' => $details['bank_name'] ?? '', 'copy' => false],
                                    'Beneficiary'    => ['val' => !empty($details['account_name']) ? $details['account_name'] : ($details['payee_name'] ?? ''), 'copy' => false],
                                    'Account Number' => ['val' => $details['account_number'] ?? '', 'copy' => true],
                                    'IFSC Code'      => ['val' => $details['ifsc'] ?? '', 'copy' => true],
                                    'Branch'         => ['val' => $details['branch'] ?? '', 'copy' => false],
                                ];
                                foreach ($rows as $label => $item):
                                    $val = $item['val'];
                                    if ($val === '' || $val === null) continue;
                                ?>
                                    <tr style="border-bottom: 1px solid var(--c-line-soft);">
                                        <td style="padding: .85rem 0; color: var(--c-ink-soft); width: 38%; font-weight: 500;"><?= e($label) ?></td>
                                        <td style="padding: .85rem 0; font-weight: 600; color: var(--c-ink);">
                                            <?php if ($item['copy']): ?>
                                                <div style="display: flex; align-items: center; gap: .6rem; flex-wrap: wrap;">
                                                    <code style="background: var(--c-cream-2); padding: .25rem .6rem; border-radius: 6px; font-size: 1rem; color: var(--c-maroon); font-weight: 700;"><?= e($val) ?></code>
                                                    <button type="button" class="copy-btn" onclick="copyToClipboard('<?= e($val) ?>', this)" title="Copy <?= e($label) ?>">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                        Copy
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <?= e($val) ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- 3-Step Instant Activation Timeline & WhatsApp Verification -->
                    <div class="payment-card" style="background: linear-gradient(135deg, #FFFDFB 0%, #FAF3EB 100%);">
                        <div class="payment-card-header">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: #E8F5E9; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                ⚡
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 1.25rem;">3-Step Instant Activation</h3>
                                <span style="font-size: .8rem; color: #166534; font-weight: 600;">Fast Activation Within 15–30 Minutes</span>
                            </div>
                        </div>

                        <div class="payment-timeline">
                            <div class="timeline-step">
                                <div class="timeline-num">1</div>
                                <div class="timeline-body">
                                    <h5>Make Payment</h5>
                                    <p>Transfer the exact package amount via UPI QR scan or NEFT/IMPS bank transfer.</p>
                                </div>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-num">2</div>
                                <div class="timeline-body">
                                    <h5>Save Transaction Screenshot / UTR</h5>
                                    <p>Note your 12-digit UTR reference number or capture a screenshot of the successful transaction.</p>
                                </div>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-num">3</div>
                                <div class="timeline-body">
                                    <h5>Send Receipt for Priority Activation</h5>
                                    <p>Share your receipt on our official WhatsApp or email. Our team will verify and upgrade your account immediately!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Big WhatsApp Screenshot Submission Button -->
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--c-line);">
                            <a href="https://wa.me/<?= e($cleanWaPhone) ?>?text=Namaste%20SpiritualShaadi%20team,%20I%20have%20completed%20the%20membership%20payment.%20Here%20is%20my%20transaction%20receipt:" target="_blank" rel="noopener" class="contact-whatsapp-btn" style="padding: 1rem 1.5rem; font-size: 1.02rem;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                Send Payment Receipt on WhatsApp
                            </a>
                            <div style="text-align: center; margin-top: .75rem; font-size: .84rem; color: var(--c-muted);">
                                Alternatively email: <a href="mailto:<?= e($contactEmail) ?>" style="color: var(--c-maroon); font-weight: 600;"><?= e($contactEmail) ?></a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Bottom Navigation Bar -->
            <div class="text-center" style="margin-top: 3rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="/packages" class="btn btn-gold btn-lg">
                    ← Back to Membership Packages
                </a>
                <a href="/contact" class="btn btn-ghost btn-lg">
                    Need Help? Contact Concierge
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
