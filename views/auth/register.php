<div class="onboarding-step-header text-center mb-4">
    <!-- Progress Indicator (Shaadi/Jeevansathi Step 1 of 4) -->
    <div class="wizard-progress-bar-wrap mb-3">
        <div class="wizard-progress-track">
            <div class="wizard-progress-fill" style="width: 25%;"></div>
        </div>
        <div class="wizard-step-badge">Step 1 of 4 · Account Basics</div>
    </div>
    <h1 style="font-family: var(--f-display); margin-bottom: .25em; font-size: 1.85rem; color: var(--c-maroon);">Begin your sacred journey</h1>
    <p style="color: var(--c-muted); font-size: .95rem; margin: 0;">Create your account to connect with genuine spiritual seekers.</p>
</div>

<form method="post" action="/register" id="registerForm">
    <?= csrf_field() ?>

    <!-- Profile Created For (Interactive Pills) -->
    <div class="field mb-4">
        <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem; display: block;">This profile is for <span class="text-danger">*</span></label>
        <div class="pill-selector-group" role="radiogroup" aria-label="Profile created for">
            <label class="pill-selector-item">
                <input type="radio" name="created_for" value="self" checked>
                <span class="pill-selector-btn">Myself</span>
            </label>
            <label class="pill-selector-item">
                <input type="radio" name="created_for" value="parent">
                <span class="pill-selector-btn">Son / Daughter</span>
            </label>
            <label class="pill-selector-item">
                <input type="radio" name="created_for" value="sibling">
                <span class="pill-selector-btn">Brother / Sister</span>
            </label>
            <label class="pill-selector-item">
                <input type="radio" name="created_for" value="relative">
                <span class="pill-selector-btn">Relative / Friend</span>
            </label>
        </div>
    </div>

    <div class="field">
        <label>Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" required autofocus placeholder="e.g. Anjali Sharma" autocomplete="name">
    </div>

    <div class="form-grid">
        <div class="field">
            <label>Gender <span class="text-danger">*</span></label>
            <select name="gender" required id="reg-gender">
                <option value="">Choose...</option>
                <option value="female">Woman (Bride)</option>
                <option value="male">Man (Groom)</option>
            </select>
        </div>
        <div class="field">
            <label>Date of Birth <span class="text-danger">*</span></label>
            <input type="date" name="dob" required max="<?= date('Y-m-d', strtotime('-18 years')) ?>" title="Must be at least 18 years old">
        </div>
    </div>

    <div class="field">
        <label>Email Address <span class="text-danger">*</span></label>
        <input type="email" name="email" required placeholder="you@example.com" autocomplete="email">
        <span class="field-help" style="font-size: .8rem; color: var(--c-muted);">We'll send a 6-digit verification code to this email.</span>
    </div>

    <div class="field">
        <label>Mobile Number <span class="text-danger">*</span></label>
        <div class="input-phone-group" style="display: flex; gap: .5rem;">
            <span class="phone-prefix" style="display: flex; align-items: center; justify-content: center; padding: 0 .85rem; background: rgba(0,0,0,0.04); border: 1.5px solid var(--c-line); border-radius: var(--r-sm); font-weight: 600; color: var(--c-ink-soft); font-size: .9rem;">+91</span>
            <input type="tel" name="phone" required placeholder="98765 43210" pattern="[0-9]{10}" maxlength="10" title="Please enter a valid 10-digit mobile number" style="flex: 1;">
        </div>
    </div>

    <div class="field">
        <label>Choose a Password <span class="text-danger">*</span></label>
        <input type="password" name="password" id="reg-password" required minlength="6" placeholder="At least 6 characters" autocomplete="new-password">
        <div class="flex-between mt-1">
            <span class="field-help" style="font-size: .8rem; color: var(--c-muted);">At least 6 characters.</span>
            <label class="show-password-label" style="font-size: .82rem; cursor: pointer; display: flex; align-items: center; gap: .3rem;">
                <input type="checkbox" class="show-password-checkbox" data-target="reg-password">
                <span>Show</span>
            </label>
        </div>
    </div>

    <label style="display: flex; gap: .6rem; align-items: flex-start; margin: 1.25rem 0 1.5rem; font-size: .88rem; color: var(--c-ink-soft); line-height: 1.45; cursor: pointer;">
        <input type="checkbox" name="agree" value="1" required style="margin-top: .2rem; flex-shrink: 0;">
        <span>
            I am at least 18 years old and I agree to the
            <a href="/page/terms" target="_blank" rel="noopener" style="color: var(--c-rose); text-decoration: underline;">Terms</a>,
            <a href="/page/privacy" target="_blank" rel="noopener" style="color: var(--c-rose); text-decoration: underline;">Privacy Policy</a>
            and <a href="/page/cookie-policy" target="_blank" rel="noopener" style="color: var(--c-rose); text-decoration: underline;">Cookie Policy</a>.
        </span>
    </label>

    <button type="submit" class="btn btn-primary btn-block btn-lg" style="box-shadow: 0 4px 16px rgba(212, 91, 122, 0.35);">
        Continue to Step 2 →
    </button>
</form>

<div class="small-link text-center mt-3" style="font-size: .92rem; color: var(--c-muted);">
    Already registered? <a href="/login" style="font-weight: 600; color: var(--c-rose);">Sign in</a>
</div>
