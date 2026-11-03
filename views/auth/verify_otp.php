<?php /** @var string $email */
/** @var string|null $mode */
/** @var int|null $cooldown */
/** @var int|null $hourlyWaitMins */
$isSignup = ($mode ?? 'reset') === 'signup';
$formAction = $isSignup ? '/verify-signup-otp' : '/verify-otp';
$resendAction = $isSignup ? '/resend-signup-otp' : '/resend-otp';
$startOverUrl = $isSignup ? '/register' : '/forgot-password';
$heading = $isSignup ? 'Verify your email' : 'Enter the 6-digit code';
$button = $isSignup ? 'Create my account' : 'Verify code';
$footerPrompt = $isSignup ? 'Already have an account?' : 'Remembered it?';
$cooldownSec = max(0, (int)($cooldown ?? 0));
$hourlyBlocked = max(0, (int)($hourlyWaitMins ?? 0));
$masked = (function ($e) {
    if (!str_contains($e, '@')) return $e;
    [$user, $domain] = explode('@', $e, 2);
    $u = strlen($user) <= 2 ? $user : substr($user, 0, 2) . str_repeat('*', max(1, strlen($user) - 2));
    return $u . '@' . $domain;
})($email);
?>
<div class="text-center mb-4">
    <h1 style="margin-bottom: .2em;"><?= e($heading) ?></h1>
    <p style="color: var(--c-muted);">We sent a code to <strong><?= e($masked) ?></strong>. It's valid for 10 minutes.</p>
</div>

<form method="post" action="<?= e($formAction) ?>">
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
               placeholder="000000"
               style="letter-spacing: .5em; text-align: center; font-size: 1.5em; font-family: monospace;">
    </div>
    <button class="btn btn-primary btn-block btn-lg"><?= e($button) ?></button>
</form>

<form method="post" action="<?= e($resendAction) ?>" id="resendOtpForm" style="margin-top: 1.25em;">
    <?= csrf_field() ?>
    <?php if ($hourlyBlocked > 0): ?>
        <p style="font-size: 0.85rem; color: #b45309; text-align: center; margin-bottom: 0.5rem; background: #fef3c7; border: 1px solid #fde68a; padding: 8px 12px; border-radius: 6px;">
            ⚠️ Resend limit reached (5 attempts/hr). Please wait <strong><?= $hourlyBlocked ?> min</strong> before requesting another code.
        </p>
        <button type="button"
                class="btn btn-link btn-block"
                disabled
                style="background: none; border: 0; color: var(--c-muted); cursor: not-allowed; font-size: 0.92rem; opacity: 0.55;">
            Resend locked (hourly limit)
        </button>
    <?php else: ?>
        <button type="submit"
                id="resendOtpBtn"
                class="btn btn-link btn-block"
                <?= $cooldownSec > 0 ? 'disabled' : '' ?>
                style="background: none; border: 0; color: var(--c-muted); cursor: <?= $cooldownSec > 0 ? 'not-allowed' : 'pointer' ?>; font-size: 0.92rem; transition: opacity .2s; <?= $cooldownSec > 0 ? 'opacity: 0.65;' : '' ?>">
            <?= $cooldownSec > 0 ? 'Resend code in ' . $cooldownSec . 's' : "Didn't receive it? Resend code" ?>
        </button>
    <?php endif; ?>
</form>

<div class="small-link">
    Wrong email? <a href="<?= e($startOverUrl) ?>">Start over</a>
</div>
<div class="small-link" style="color: var(--c-muted);">
    <?= e($footerPrompt) ?> <a href="/login">Back to sign in</a>
</div>

<?php if ($hourlyBlocked <= 0): ?>
<script>
(function() {
    var cooldown = <?= $cooldownSec ?>;
    var btn = document.getElementById('resendOtpBtn');
    var form = document.getElementById('resendOtpForm');
    if (!btn || !form) return;

    var timer = null;

    function renderCooldown() {
        if (cooldown > 0) {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            btn.style.opacity = '0.65';
            btn.textContent = 'Resend code in ' + cooldown + 's';
        } else {
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            btn.style.opacity = '1';
            btn.textContent = "Didn't receive it? Resend code";
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }
    }

    if (cooldown > 0) {
        timer = setInterval(function() {
            cooldown--;
            renderCooldown();
        }, 1000);
    }

    form.addEventListener('submit', function(e) {
        if (cooldown > 0 || btn.disabled) {
            e.preventDefault();
            return false;
        }
        btn.disabled = true;
        btn.style.opacity = '0.65';
        btn.textContent = 'Sending fresh code...';
    });
})();
</script>
<?php endif; ?>
