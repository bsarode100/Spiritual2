<div class="text-center mb-4">
    <h1 style="margin-bottom: .2em;">Begin your journey</h1>
    <p style="color: var(--c-muted);">Create your free Sadhak account. Takes 60 seconds.</p>
</div>

<form method="post" action="/register">
    <?= csrf_field() ?>
    <div class="field">
        <label>Full name</label>
        <input type="text" name="name" required autofocus placeholder="Anjali Sharma">
    </div>
    <div class="form-grid">
        <div class="field">
            <label>I am</label>
            <select name="gender" required>
                <option value="">Choose...</option>
                <option value="female">A woman</option>
                <option value="male">A man</option>
            </select>
        </div>
        <div class="field">
            <label>Date of birth</label>
            <input type="date" name="dob" required>
        </div>
    </div>
    <div class="field">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="field">
        <label>Phone <span style="opacity:.6;">(optional)</span></label>
        <input type="tel" name="phone" placeholder="+91 9XXXXX XXXXX">
    </div>
    <div class="field">
        <label>Choose a password</label>
        <input type="password" name="password" required minlength="6">
        <span class="field-help">At least 6 characters.</span>
    </div>
    <label style="display: flex; gap: .55rem; align-items: flex-start; margin: 1rem 0 1.25rem; font-size: .9rem; color: var(--c-ink-soft); line-height: 1.45;">
        <input type="checkbox" name="agree" value="1" required style="margin-top: .25rem;">
        <span>
            I am at least 18 years old and I have read and agree to the
            <a href="/page/terms-and-condition" target="_blank" rel="noopener">Terms &amp; Conditions</a>,
            <a href="/page/privacy-policy" target="_blank" rel="noopener">Privacy Policy</a>,
            <a href="/refund-policy" target="_blank" rel="noopener">Refund Policy</a>
            and <a href="/cookies" target="_blank" rel="noopener">Cookie Policy</a>.
        </span>
    </label>
    <button class="btn btn-primary btn-block btn-lg">Create My Account</button>
</form>

<div class="small-link">
    Already a seeker here? <a href="/login">Sign in</a>
</div>
