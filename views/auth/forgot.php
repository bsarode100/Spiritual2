<?php
$cooldownSec = max(0, (int)($cooldown ?? 0));
$hourlyBlocked = max(0, (int)($hourlyWaitMins ?? 0));
?>
<div class="text-center mb-4">
    <h1 style="margin-bottom: .2em;">Forgot password?</h1>
    <p style="color: var(--c-muted);">Enter your registered email and we'll send a 6-digit code.</p>
</div>

<?php if ($hourlyBlocked > 0): ?>
    <div style="font-size: 0.88rem; color: #b45309; text-align: center; margin-bottom: 1.2rem; background: #fef3c7; border: 1px solid #fde68a; padding: 10px 14px; border-radius: 8px;">
        ⚠️ Maximum OTP requests reached (5 attempts/hr). Please wait <strong><?= $hourlyBlocked ?> minutes</strong> before requesting a new code, or <a href="/contact" style="color:#b45309; text-decoration:underline;">contact support</a>.
    </div>
<?php endif; ?>

<form method="post" action="/forgot-password" onsubmit="var b=this.querySelector('button[type=submit]'); if(b){b.disabled=true; b.textContent='Sending code...';}">
    <?= csrf_field() ?>
    <!-- Bot Protection Honeypot -->
    <div style="display:none !important; visibility:hidden; height:0; width:0; overflow:hidden;" aria-hidden="true">
        <input type="text" name="b_hp_email" tabindex="-1" autocomplete="off" value="">
        <input type="hidden" name="form_loaded_at" value="<?= time() ?>">
    </div>
    <div class="field">
        <label>Email address</label>
        <input type="email" name="email" value="<?= e($email ?? '') ?>" required autofocus placeholder="you@example.com">
    </div>
    <button type="submit" class="btn btn-primary btn-block btn-lg" <?= $hourlyBlocked > 0 ? 'disabled style="opacity:0.6; cursor:not-allowed;"' : '' ?>>
        Send 6-digit code
    </button>
</form>

<?php if (!empty($email)): ?>
<div class="small-link" style="margin-top: 1rem;">
    Already requested a code? <a href="/verify-otp">Enter your code here</a>
</div>
<?php endif; ?>

<div class="small-link">
    Remembered it? <a href="/login">Back to sign in</a>
</div>
<div class="small-link" style="color: var(--c-muted);">
    Didn't get an email? <a href="/contact">Contact support</a> and we'll help you in.
</div>
