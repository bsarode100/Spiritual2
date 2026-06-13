<?php /** @var array $values */ ?>
<div class="admin-head">
    <h1>Payment Details</h1>
    <a href="/payment-details" target="_blank" class="btn btn-ghost btn-sm">↗ View public page</a>
</div>

<form method="post" action="/admin/payment-details">
    <?= csrf_field() ?>

    <div class="admin-card mb-3">
        <h3>UPI</h3>
        <p style="color: var(--c-muted);">UPI is the quickest way for members in India to pay. Leave any field blank to hide it on the public page.</p>
        <div class="form-grid">
            <div class="field">
                <label>Payee / Business name</label>
                <input type="text" name="payment_payee_name" value="<?= e($values['payment_payee_name']) ?>" placeholder="e.g. Spiritual Matrimony">
            </div>
            <div class="field">
                <label>UPI ID</label>
                <input type="text" name="payment_upi_id" value="<?= e($values['payment_upi_id']) ?>" placeholder="e.g. spiritualmatrimony@upi">
            </div>
        </div>
        <div class="field">
            <label>UPI QR code image URL</label>
            <input type="url" name="payment_upi_qr_url" value="<?= e($values['payment_upi_qr_url']) ?>" placeholder="https://your-cdn.com/upi-qr.png">
            <small style="color: var(--c-muted);">Upload your QR image anywhere (S3, Cloudinary, /uploads/site) and paste the URL here. The image is shown on the public page when this is set.</small>
        </div>
    </div>

    <div class="admin-card mb-3">
        <h3>Bank Transfer</h3>
        <p style="color: var(--c-muted);">Members can use NEFT/IMPS/RTGS if UPI is not an option.</p>
        <div class="form-grid">
            <div class="field">
                <label>Bank name</label>
                <input type="text" name="payment_bank_name" value="<?= e($values['payment_bank_name']) ?>" placeholder="e.g. HDFC Bank">
            </div>
            <div class="field">
                <label>Account holder name</label>
                <input type="text" name="payment_account_name" value="<?= e($values['payment_account_name']) ?>" placeholder="As per bank records">
            </div>
            <div class="field">
                <label>Account number</label>
                <input type="text" name="payment_account_number" value="<?= e($values['payment_account_number']) ?>" placeholder="e.g. 1234567890123">
            </div>
            <div class="field">
                <label>IFSC code</label>
                <input type="text" name="payment_ifsc" value="<?= e($values['payment_ifsc']) ?>" placeholder="e.g. HDFC0001234">
            </div>
            <div class="field">
                <label>Branch</label>
                <input type="text" name="payment_branch" value="<?= e($values['payment_branch']) ?>" placeholder="e.g. MG Road, Bengaluru">
            </div>
        </div>
    </div>

    <div class="admin-card mb-3">
        <h3>Support &amp; Activation</h3>
        <p style="color: var(--c-muted);">Tell members how to reach you after they pay and how long activation will take.</p>
        <div class="form-grid">
            <div class="field">
                <label>Support phone</label>
                <input type="tel" name="payment_contact_phone" value="<?= e($values['payment_contact_phone']) ?>" placeholder="e.g. +91 98765 43210">
            </div>
            <div class="field">
                <label>Support email</label>
                <input type="email" name="payment_contact_email" value="<?= e($values['payment_contact_email']) ?>" placeholder="e.g. support@spiritualmatrimony.com">
            </div>
        </div>
        <div class="field">
            <label>Instructions shown to members</label>
            <textarea name="payment_instructions" rows="4" placeholder="Tell members what to do after they pay (e.g., share screenshot on WhatsApp, expected activation time)."><?= e($values['payment_instructions']) ?></textarea>
        </div>
    </div>

    <button class="btn btn-primary btn-lg">Save Payment Details</button>
</form>
