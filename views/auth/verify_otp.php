<?php /** @var string $email */
$masked = (function ($e) {
    if (!str_contains($e, '@')) return $e;
    [$user, $domain] = explode('@', $e, 2);
    $u = strlen($user) <= 2 ? $user : substr($user, 0, 2) . str_repeat('*', max(1, strlen($user) - 2));
    return $u . '@' . $domain;
})($email);
?>
<div class="text-center mb-4">
    <h1 style="margin-bottom: .2em;">Enter the 6-digit code</h1>
    <p style="color: var(--c-muted);">We sent a code to <strong><?= e($masked) ?></strong>. It's valid for 10 minutes.</p>
</div>

<form method="post" action="/verify-otp">
    <?= csrf_field() ?>
    <div class="field">
        <label>6-digit code</label>
        <input type="text"
               name="otp"
               required
               autofocus
               inputmode="numeric"
               pattern="[0-9]{6}"
               maxlength="6"
               autocomplete="one-time-code"
               placeholder="••••••"
               style="letter-spacing: .5em; text-align: center; font-size: 1.5em; font-family: monospace;">
    </div>
    <button class="btn btn-primary btn-block btn-lg">Verify code</button>
</form>

<form method="post" action="/resend-otp" style="margin-top: 1em;">
    <?= csrf_field() ?>
    <button type="submit"
            class="btn btn-link btn-block"
            style="background: none; border: 0; color: var(--c-muted); cursor: pointer;">
        Didn't receive it? Resend code
    </button>
</form>

<div class="small-link">
    Wrong email? <a href="/forgot-password">Start over</a>
</div>
<div class="small-link" style="color: var(--c-muted);">
    Remembered it? <a href="/login">Back to sign in</a>
</div>
