<?php /** @var string $token */ ?>
<div class="text-center mb-4">
    <h1 style="margin-bottom: .2em;">Set a new password</h1>
    <p style="color: var(--c-muted);">Choose a password you'll remember — at least 6 characters.</p>
</div>

<form method="post" action="/reset-password/<?= e($token) ?>">
    <?= csrf_field() ?>
    <div class="field">
        <label>New password</label>
        <input type="password" name="password" required autofocus minlength="6" placeholder="At least 6 characters">
    </div>
    <div class="field">
        <label>Confirm password</label>
        <input type="password" name="password_confirm" required minlength="6">
    </div>
    <button class="btn btn-primary btn-block btn-lg">Update password</button>
</form>

<div class="small-link">
    <a href="/login">Back to sign in</a>
</div>
