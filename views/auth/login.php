<div class="text-center mb-4">
    <h1 style="margin-bottom: .2em;">Welcome back</h1>
    <p style="color: var(--c-muted);">Sign in to continue your sacred journey.</p>
</div>

<form method="post" action="/login">
    <?= csrf_field() ?>
    <div class="field">
        <label>Email</label>
        <input type="email" name="email" required autofocus>
    </div>
    <div class="field">
        <label style="display:flex; justify-content:space-between; align-items:baseline;">
            <span>Password</span>
            <a href="/forgot-password" style="font-weight:400; font-size:.88rem;">Forgot password?</a>
        </label>
        <input type="password" name="password" required>
    </div>
    <button class="btn btn-primary btn-block btn-lg">Sign In</button>
    <p style="margin-top: 1rem; font-size: .78rem; color: var(--c-ink-soft); line-height: 1.5; text-align: center;">
        By signing in, you agree to our
        <a href="/page/terms-and-condition" target="_blank" rel="noopener">Terms &amp; Conditions</a>
        and <a href="/page/privacy-policy" target="_blank" rel="noopener">Privacy Policy</a>.
    </p>
</form>

<div class="small-link">
    New here? <a href="/register">Create your free account</a>
</div>

<div class="small-link" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px dashed var(--c-line); color: var(--c-muted);">
    <strong>Demo accounts</strong><br>
    Admin: admin@spiritual2.test / admin@123<br>
    Member: anjali@example.com / member@123
</div>
