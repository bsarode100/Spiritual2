<?php
$cooldownSec = max(0, (int)($cooldown ?? 0));
?>
<div class="text-center mb-4">
    <h1 style="margin-bottom: .2em;">Forgot password?</h1>
    <p style="color: var(--c-muted);">Enter your registered email and we'll send a 6-digit code.</p>
</div>

<form method="post" action="/forgot-password" onsubmit="var b=this.querySelector('button[type=submit]'); if(b){b.disabled=true; b.textContent='Sending code...';}">
    <?= csrf_field() ?>
    <div class="field">
        <label>Email address</label>
        <input type="email" name="email" value="<?= e($email ?? '') ?>" required autofocus placeholder="you@example.com">
    </div>
    <button type="submit" class="btn btn-primary btn-block btn-lg">Send 6-digit code</button>
</form>

<?php if (!empty($email) && $cooldownSec > 0): ?>
<div class="small-link" style="margin-top: 1rem;">
    Already requested a code? <a href="/verify-otp">Enter your code (<?= $cooldownSec ?>s cooldown)</a>
</div>
<?php endif; ?>

<div class="small-link">
    Remembered it? <a href="/login">Back to sign in</a>
</div>
<div class="small-link" style="color: var(--c-muted);">
    Didn't get an email? <a href="/contact">Contact support</a> and we'll help you in.
</div>
