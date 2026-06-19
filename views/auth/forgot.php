<div class="text-center mb-4">
    <h1 style="margin-bottom: .2em;">Forgot password?</h1>
    <p style="color: var(--c-muted);">Enter your registered email and we'll send a 6-digit code.</p>
</div>

<form method="post" action="/forgot-password">
    <?= csrf_field() ?>
    <div class="field">
        <label>Email address</label>
        <input type="email" name="email" required autofocus placeholder="you@example.com">
    </div>
    <button class="btn btn-primary btn-block btn-lg">Send 6-digit code</button>
</form>

<div class="small-link">
    Remembered it? <a href="/login">Back to sign in</a>
</div>
<div class="small-link" style="color: var(--c-muted);">
    Didn't get an email? <a href="/contact">Contact support</a> and we'll help you in.
</div>
