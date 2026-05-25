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
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button class="btn btn-primary btn-block btn-lg">Sign In</button>
</form>

<div class="small-link">
    New here? <a href="/register">Create your free account</a>
</div>

<div class="small-link" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px dashed var(--c-line); color: var(--c-muted);">
    <strong>Demo accounts</strong><br>
    Admin: admin@spiritual2.test / admin@123<br>
    Member: anjali@example.com / member@123
</div>
