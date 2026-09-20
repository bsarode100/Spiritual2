<?php
/** @var array $profile, $spiritual, $user; @var int $currentStep, $photoCount */
?>
<div class="onboarding-wrapper">
    <!-- Top Progress Bar (Shaadi/Jeevansathi Style) -->
    <div class="onboarding-progress-header">
        <div class="container-sm">
            <div class="wizard-progress-bar-wrap mb-2">
                <div class="wizard-progress-track">
                    <div class="wizard-progress-fill" id="wizardProgressFill" style="width: <?= $currentStep === 3 ? '75%' : ($currentStep === 4 ? '100%' : '50%') ?>;"></div>
                </div>
                <div class="flex-between wizard-step-nav-info">
                    <span class="wizard-step-badge" id="wizardStepBadge">Step <span id="currentStepNum"><?= $currentStep ?></span> of 4</span>
                    <span class="wizard-step-title" id="wizardStepTitle">
                        <?= $currentStep === 3 ? 'Location, Career & Lifestyle' : ($currentStep === 4 ? 'About You & Photos' : 'Cultural & Spiritual Roots') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-sm">
        <div class="onboarding-card">
            <form method="post" action="/onboarding" id="onboardingForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="step" id="formStepInput" value="<?= $currentStep ?>">

                <!-- =======================================================
                     STEP 2: CULTURAL & SPIRITUAL ROOTS (Platform Core USP)
                     ======================================================= -->
                <div class="onboarding-step-pane <?= $currentStep === 2 ? 'is-active' : '' ?>" id="stepPane2" data-step="2">
                    <div class="step-title-box text-center mb-4">
                        <div class="spiritual-icon-badge">🪷</div>
                        <h2>Cultural &amp; Spiritual Roots</h2>
                        <p class="text-muted">Tell us about your background and spiritual journey — this is the heart of SpiritualShaadi.</p>
                    </div>

                    <div class="form-grid mb-3">
                        <!-- Height -->
                        <div class="field">
                            <label>Height <span class="text-danger">*</span></label>
                            <select name="height_cm" required>
                                <option value="">Select height...</option>
                                <?php for ($h = 137; $h <= 213; $h++): ?>
                                    <option value="<?= $h ?>" <?= (int)($profile['height_cm'] ?? 165) === $h ? 'selected' : '' ?>>
                                        <?= cm_to_feet($h) ?> (<?= $h ?> cm)
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Marital Status (Pills) -->
                        <div class="field">
                            <label>Marital Status <span class="text-danger">*</span></label>
                            <div class="pill-selector-group" role="radiogroup">
                                <?php foreach (marital_status_options() as $val => $lbl): ?>
                                    <label class="pill-selector-item">
                                        <input type="radio" name="marital_status" value="<?= $val ?>" <?= ($profile['marital_status'] ?? 'never_married') === $val ? 'selected checked' : '' ?> required>
                                        <span class="pill-selector-btn"><?= e($lbl) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid mb-3">
                        <!-- Religion -->
                        <div class="field">
                            <label>Religion <span class="text-danger">*</span></label>
                            <select name="religion" required>
                                <?php foreach (['Hindu','Jain','Sikh','Buddhist','Spiritual - Not Religious','Other'] as $r): ?>
                                    <option value="<?= $r ?>" <?= ($profile['religion'] ?? 'Hindu') === $r ? 'selected' : '' ?>><?= $r ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Mother Tongue -->
                        <div class="field">
                            <label>Mother Tongue <span class="text-danger">*</span></label>
                            <select name="mother_tongue" required>
                                <option value="">Select language...</option>
                                <?php foreach (['Hindi','Marathi','Gujarati','Bengali','Punjabi','Tamil','Telugu','Kannada','Malayalam','Odia','Assamese','Marwari','Sindhi','English','Other'] as $lang): ?>
                                    <option value="<?= $lang ?>" <?= ($profile['mother_tongue'] ?? '') === $lang ? 'selected' : '' ?>><?= $lang ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Caste / Community with Instant Chips -->
                    <div class="field mb-4">
                        <label>Caste / Community <span class="text-danger">*</span></label>
                        <input type="text" name="community" id="communityInput" required placeholder="e.g. Brahmin, Vaishnav, Maratha, etc." value="<?= e($profile['community'] ?? '') ?>">
                        <div class="quick-chips mt-2">
                            <span class="quick-chip" onclick="setFieldValue('communityInput', 'Caste No Bar')">✨ Caste No Bar</span>
                            <span class="quick-chip" onclick="setFieldValue('communityInput', 'Don\'t Know')">Don't Know</span>
                        </div>
                    </div>

                    <!-- 🌟 Spiritual Path / Sampradaya (CORE USP) -->
                    <div class="spiritual-highlight-box mb-4">
                        <label class="spiritual-section-label">
                            <span>🌟 Spiritual Path / Tradition <span class="text-danger">*</span></span>
                            <small>Select the path that resonates with your daily practice</small>
                        </label>
                        <div class="pill-selector-group spiritual-pills" role="radiogroup">
                            <?php foreach (spiritual_paths_list() as $path): ?>
                                <label class="pill-selector-item">
                                    <input type="radio" name="spiritual_path" value="<?= $path ?>" <?= ($spiritual['spiritual_path'] ?? '') === $path ? 'checked' : '' ?> required>
                                    <span class="pill-selector-btn spiritual-pill-btn"><?= e($path) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 🌟 Spiritual Organization / Guru -->
                    <div class="field mb-4">
                        <label>Spiritual Organization / Guru <span style="opacity: .7;">(Recommended)</span></label>
                        <input type="text" name="guru" id="guruInput" placeholder="e.g. ISKCON, Art of Living, Isha Foundation, Ramakrishna Math, or Mentor name" value="<?= e($spiritual['guru'] ?? '') ?>">
                        <div class="quick-chips mt-2">
                            <span class="quick-chip" onclick="setFieldValue('guruInput', 'ISKCON')">ISKCON</span>
                            <span class="quick-chip" onclick="setFieldValue('guruInput', 'Art of Living')">Art of Living</span>
                            <span class="quick-chip" onclick="setFieldValue('guruInput', 'Isha Foundation')">Isha Foundation</span>
                            <span class="quick-chip" onclick="setFieldValue('guruInput', 'Ramakrishna Math')">Ramakrishna Math</span>
                            <span class="quick-chip" onclick="setFieldValue('guruInput', 'Self-Guided')">Self-Guided</span>
                        </div>
                    </div>

                    <div class="wizard-btn-row">
                        <a href="/register" class="btn btn-ghost">← Previous</a>
                        <button type="button" class="btn btn-primary btn-lg" onclick="goToStep(3)">Continue to Step 3 →</button>
                    </div>
                </div>

                <!-- =======================================================
                     STEP 3: LOCATION, EDUCATION, CAREER & LIFESTYLE
                     ======================================================= -->
                <div class="onboarding-step-pane <?= $currentStep === 3 ? 'is-active' : '' ?>" id="stepPane3" data-step="3">
                    <div class="step-title-box text-center mb-4">
                        <div class="spiritual-icon-badge">🌿</div>
                        <h2>Location, Career &amp; Lifestyle</h2>
                        <p class="text-muted">Help compatible seekers in your preferred location and lifestyle connect with you.</p>
                    </div>

                    <!-- Location Grid -->
                    <div class="form-grid mb-3">
                        <div class="field">
                            <label>Country of Residence <span class="text-danger">*</span></label>
                            <select name="country" id="countrySelect" required>
                                <?php foreach (['India','United States','United Kingdom','Canada','Australia','United Arab Emirates','Singapore','Other'] as $c): ?>
                                    <option value="<?= $c ?>" <?= ($profile['country'] ?? 'India') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>State / Province <span class="text-danger">*</span></label>
                            <input type="text" name="state" required placeholder="e.g. Maharashtra, Gujarat, Delhi" value="<?= e($profile['state'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="field mb-3">
                        <label>City <span class="text-danger">*</span></label>
                        <input type="text" name="city" required placeholder="e.g. Mumbai, Pune, Ahmedabad, Bangalore" value="<?= e($profile['city'] ?? '') ?>">
                    </div>

                    <!-- Education & Profession -->
                    <div class="form-grid mb-3">
                        <div class="field">
                            <label>Highest Education <span class="text-danger">*</span></label>
                            <select name="education" required>
                                <option value="">Select education...</option>
                                <?php foreach (['Bachelors (B.Tech / B.E / B.Sc / B.Com / B.A)','Masters (M.Tech / M.S / M.Sc / M.A)','MBA / PGDM','Doctorate / Ph.D','CA / CS / CFA','MBBS / MD / Dental','Law (LLB / LLM)','Diploma','High School','Other'] as $edu): ?>
                                    <option value="<?= $edu ?>" <?= ($profile['education'] ?? '') === $edu ? 'selected' : '' ?>><?= $edu ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Occupation / Profession <span class="text-danger">*</span></label>
                            <input type="text" name="profession" required placeholder="e.g. Software Engineer, Doctor, Business Owner, Teacher" value="<?= e($profile['profession'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Annual Income (Pills with "Prefer not to say" option) -->
                    <div class="field mb-4">
                        <label>Annual Income <span class="text-danger">*</span></label>
                        <div class="pill-selector-group" role="radiogroup">
                            <?php foreach (annual_income_options() as $val => $lbl): ?>
                                <label class="pill-selector-item">
                                    <input type="radio" name="annual_income" value="<?= $val ?>" <?= ($profile['annual_income'] ?? 'Prefer not to say') === $val ? 'checked' : '' ?> required>
                                    <span class="pill-selector-btn <?= $val === 'Prefer not to say' ? 'pill-muted' : '' ?>"><?= e($lbl) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Diet (Pills) -->
                    <div class="field mb-4">
                        <label>Diet / Food Preference <span class="text-danger">*</span></label>
                        <div class="pill-selector-group" role="radiogroup">
                            <?php foreach (diet_options() as $val => $lbl): ?>
                                <label class="pill-selector-item">
                                    <input type="radio" name="diet" value="<?= $val ?>" <?= ($profile['diet'] ?? 'vegetarian') === $val ? 'checked' : '' ?> required>
                                    <span class="pill-selector-btn"><?= e($lbl) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="wizard-btn-row">
                        <button type="button" class="btn btn-ghost" onclick="goToStep(2)">← Previous</button>
                        <button type="button" class="btn btn-primary btn-lg" onclick="goToStep(4)">Continue to Step 4 →</button>
                    </div>
                </div>

                <!-- =======================================================
                     STEP 4: ABOUT YOU & PROFILE PHOTO VERIFICATION
                     ======================================================= -->
                <div class="onboarding-step-pane <?= $currentStep === 4 ? 'is-active' : '' ?>" id="stepPane4" data-step="4">
                    <div class="step-title-box text-center mb-4">
                        <div class="spiritual-icon-badge">✨</div>
                        <h2>About You &amp; Photo Verification</h2>
                        <p class="text-muted">A sincere introduction and at least one authentic photograph establish trust and higher response rates.</p>
                    </div>

                    <!-- About Me -->
                    <div class="field mb-4">
                        <label>About Me &amp; What I Seek in a Life Partner <span class="text-danger">*</span></label>
                        <textarea name="about_me" id="aboutMeText" rows="5" required minlength="30" placeholder="Share a few heartfelt words about your values, daily spiritual practice, lifestyle, and what kind of partner you hope to walk this sacred journey with..." style="line-height: 1.6;"><?= e($profile['about_me'] ?? '') ?></textarea>
                        
                        <div class="quick-suggestion-chips mt-2">
                            <span class="suggestion-chip" onclick="appendSuggestion('I value daily sadhana, meditation, and simple living.')">
                                ➕ Daily sadhana &amp; meditation
                            </span>
                            <span class="suggestion-chip" onclick="appendSuggestion('Seeking an understanding companion on the path of self-realization.')">
                                ➕ Companion on spiritual path
                            </span>
                            <span class="suggestion-chip" onclick="appendSuggestion('I believe in balancing spiritual aspirations with family and worldly responsibilities.')">
                                ➕ Balanced life &amp; values
                            </span>
                        </div>
                    </div>

                    <!-- Profile Photo Uploader (Minimum 1 Photo) -->
                    <div class="field mb-4">
                        <label>Profile Photograph <span class="text-danger">*</span> <small class="text-muted">(Minimum 1 clear photograph required)</small></label>
                        <div class="photo-upload-dropzone" id="photoDropzone" onclick="document.getElementById('photoFileInput').click()">
                            <input type="file" name="photo" id="photoFileInput" accept="image/jpeg,image/png,image/webp" style="display: none;" onchange="handlePhotoPreview(this)">
                            <div class="photo-dropzone-content" id="photoDropzoneContent">
                                <div class="dropzone-icon">📸</div>
                                <div class="dropzone-text">
                                    <strong>Tap or click to upload your photograph</strong>
                                    <p>Clear, recent portrait photos receive 5x more meaningful responses.</p>
                                    <span class="btn btn-sm btn-ghost">Select Photo</span>
                                </div>
                            </div>
                            <div class="photo-preview-box" id="photoPreviewBox" style="display: none;">
                                <img id="photoPreviewImg" src="" alt="Photo preview">
                                <button type="button" class="btn-remove-preview" onclick="removePhotoPreview(event)">✕ Change</button>
                            </div>
                        </div>
                        <?php if ($photoCount > 0): ?>
                            <div class="mt-2 text-success" style="font-size: .88rem;">
                                ✓ You already have <?= $photoCount ?> photo(s) uploaded. You can upload an additional primary photo or proceed.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="wizard-btn-row">
                        <button type="button" class="btn btn-ghost" onclick="goToStep(3)">← Previous</button>
                        <button type="submit" class="btn btn-primary btn-lg" id="finishButton">
                            Complete Profile &amp; View Matches 🪷
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setFieldValue(fieldId, value) {
    const el = document.getElementById(fieldId);
    if (el) {
        el.value = value;
        el.focus();
    }
}

function appendSuggestion(text) {
    const area = document.getElementById('aboutMeText');
    if (!area) return;
    if (area.value.trim() === '') {
        area.value = text;
    } else {
        area.value = area.value.trim() + ' ' + text;
    }
    area.focus();
}

function goToStep(stepNum) {
    // Basic client-side validation of current step before moving forward
    const currentPane = document.querySelector('.onboarding-step-pane.is-active');
    if (currentPane) {
        const requiredInputs = currentPane.querySelectorAll('input[required], select[required], textarea[required]');
        for (let input of requiredInputs) {
            if (input.type === 'radio') {
                const group = currentPane.querySelectorAll(`input[name="${input.name}"]`);
                const anyChecked = Array.from(group).some(r => r.checked);
                if (!anyChecked) {
                    alert('Please select an option for: ' + (input.closest('.field')?.querySelector('label')?.innerText.replace('*', '').trim() || input.name));
                    return;
                }
            } else if (!input.value || input.value.trim() === '') {
                input.focus();
                alert('Please fill out: ' + (input.closest('.field')?.querySelector('label')?.innerText.replace('*', '').trim() || input.name));
                return;
            }
        }
    }

    // Switch active step pane
    document.querySelectorAll('.onboarding-step-pane').forEach(p => p.classList.remove('is-active'));
    const targetPane = document.getElementById('stepPane' + stepNum);
    if (targetPane) targetPane.classList.add('is-active');

    // Update progress bar
    const progressFill = document.getElementById('wizardProgressFill');
    const stepBadge = document.getElementById('currentStepNum');
    const stepTitle = document.getElementById('wizardStepTitle');
    const formStepInput = document.getElementById('formStepInput');

    if (stepBadge) stepBadge.innerText = stepNum;
    if (formStepInput) formStepInput.value = stepNum;

    if (stepNum === 2) {
        if (progressFill) progressFill.style.width = '50%';
        if (stepTitle) stepTitle.innerText = 'Cultural & Spiritual Roots';
    } else if (stepNum === 3) {
        if (progressFill) progressFill.style.width = '75%';
        if (stepTitle) stepTitle.innerText = 'Location, Career & Lifestyle';
    } else if (stepNum === 4) {
        if (progressFill) progressFill.style.width = '100%';
        if (stepTitle) stepTitle.innerText = 'About You & Photos';
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handlePhotoPreview(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreviewImg').src = e.target.result;
            document.getElementById('photoDropzoneContent').style.display = 'none';
            document.getElementById('photoPreviewBox').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removePhotoPreview(e) {
    e.stopPropagation();
    document.getElementById('photoFileInput').value = '';
    document.getElementById('photoPreviewBox').style.display = 'none';
    document.getElementById('photoDropzoneContent').style.display = 'block';
}
</script>
