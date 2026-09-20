<?php
/** @var array $profile, $spiritual, $user; @var int $currentStep, $photoCount */
$savedPath = trim((string)($spiritual['spiritual_path'] ?? ''));
$savedOrg  = trim((string)($spiritual['spiritual_organization'] ?? ''));
$savedCountry = trim((string)($profile['country'] ?? 'India'));
$savedState = trim((string)($profile['state'] ?? ''));
$isIndia = ($savedCountry === '' || strcasecmp($savedCountry, 'India') === 0);
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
                        <p class="text-muted">Tell us about your background and spiritual journey — this is the sacred heart of your profile.</p>
                    </div>

                    <!-- Marital Status (Full Row for comfortable pill spacing) -->
                    <div class="field mb-3">
                        <label>Marital Status <span class="text-danger">*</span></label>
                        <div class="pill-selector-group" role="radiogroup">
                            <?php foreach (marital_status_options() as $val => $lbl): ?>
                                <label class="pill-selector-item">
                                    <input type="radio" name="marital_status" value="<?= $val ?>" <?= ($profile['marital_status'] ?? 'never_married') === $val ? 'checked' : '' ?> required>
                                    <span class="pill-selector-btn"><?= e($lbl) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
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

                        <!-- Caste / Community with Dropdown / Autocomplete Datalist -->
                        <div class="field">
                            <label>Caste / Community <span class="text-danger">*</span></label>
                            <input type="text" name="community" id="communityInput" list="communityList" required placeholder="e.g. Brahmin, Vaishnav, Maratha, etc." value="<?= e($profile['community'] ?? '') ?>">
                            <datalist id="communityList">
                                <option value="Caste No Bar">
                                <option value="Brahmin">
                                <option value="Vaishnav">
                                <option value="Maratha">
                                <option value="Agarwal">
                                <option value="Gupta">
                                <option value="Maheshwari">
                                <option value="Jain - Digambar">
                                <option value="Jain - Shwetambar">
                                <option value="Patel / Patidar">
                                <option value="Rajput">
                                <option value="Kayastha">
                                <option value="Khatri">
                                <option value="Arora">
                                <option value="Reddy">
                                <option value="Nair">
                                <option value="Lingayat">
                                <option value="Yadav">
                                <option value="Bania">
                                <option value="Sindhi">
                                <option value="Punjabi">
                                <option value="Sikh - Jat">
                                <option value="Sikh - Ramgarhia">
                                <option value="Other">
                            </datalist>
                            <div class="quick-chips mt-2">
                                <span class="quick-chip" onclick="setFieldValue('communityInput', 'Caste No Bar')">✨ Caste No Bar</span>
                                <span class="quick-chip" onclick="setFieldValue('communityInput', 'Don\'t Know')">Don't Know</span>
                            </div>
                        </div>
                    </div>

                    <!-- 🌟 Spiritual Path / Sampradaya (CORE USP) -->
                    <div class="spiritual-highlight-box mb-4">
                        <label class="spiritual-section-label">
                            <span>🌟 Spiritual Path / Tradition <span class="text-danger">*</span></span>
                            <small>Select the path or tradition that guides your daily spiritual practice</small>
                        </label>

                        <div class="field mb-3">
                            <select name="spiritual_path" id="spiritualPathSelect" required onchange="handlePathChange(this.value)">
                                <option value="">Select your spiritual path / tradition...</option>
                                <optgroup label="Popular Spiritual Traditions">
                                    <?php foreach (spiritual_paths_list() as $path): ?>
                                        <?php if ($path !== 'Other'): ?>
                                            <option value="<?= e($path) ?>" <?= $savedPath === $path ? 'selected' : '' ?>><?= e($path) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Classical Yoga &amp; Philosophical Paths">
                                    <?php foreach (['Advaita Vedanta','Bhakti Yoga','Karma Yoga','Jnana Yoga','Raja Yoga / Meditation','Kashmir Shaivism','Kriya Yoga','Kundalini Yoga','Mantra Sadhana / Chanting','Sikh Dharma','Pushtimarg','Ramanandi Sampradaya','Nimbarka Sampradaya','Lingayat / Veerashaiva'] as $extraPath): ?>
                                        <?php if (!in_array($extraPath, spiritual_paths_list(), true)): ?>
                                            <option value="<?= e($extraPath) ?>" <?= $savedPath === $extraPath ? 'selected' : '' ?>><?= e($extraPath) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                                <option value="Other" <?= (!empty($savedPath) && !in_array($savedPath, spiritual_paths_list(), true) && !in_array($savedPath, ['Advaita Vedanta','Bhakti Yoga','Karma Yoga','Jnana Yoga','Raja Yoga / Meditation','Kashmir Shaivism','Kriya Yoga','Kundalini Yoga','Mantra Sadhana / Chanting','Sikh Dharma','Pushtimarg','Ramanandi Sampradaya','Nimbarka Sampradaya','Lingayat / Veerashaiva'], true)) || $savedPath === 'Other' ? 'selected' : '' ?>>
                                    ✏️ Other (Specify your tradition)
                                </option>
                            </select>
                        </div>

                        <!-- Quick-select chips for instant 1-tap choice -->
                        <div class="quick-chips mb-2">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--c-maroon); display: inline-flex; align-items: center;">Quick Select:</span>
                            <?php foreach (['ISKCON', 'Art of Living', 'Isha Yoga', 'Ramakrishna Mission / Vedanta', 'Vipassana', 'Swaminarayan', 'Self-Guided / Independent Sadhak'] as $quickP): ?>
                                <span class="quick-chip" onclick="selectSpiritualPath('<?= e($quickP) ?>')"><?= e($quickP) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Custom Path text input when "Other" is chosen -->
                        <div id="pathOtherWrap" class="field mt-2" style="<?= (!empty($savedPath) && !in_array($savedPath, spiritual_paths_list(), true) && !in_array($savedPath, ['Advaita Vedanta','Bhakti Yoga','Karma Yoga','Jnana Yoga','Raja Yoga / Meditation','Kashmir Shaivism','Kriya Yoga','Kundalini Yoga','Mantra Sadhana / Chanting','Sikh Dharma','Pushtimarg','Ramanandi Sampradaya','Nimbarka Sampradaya','Lingayat / Veerashaiva'], true)) || $savedPath === 'Other' ? 'display:block;' : 'display:none;' ?>">
                            <label style="font-size: 0.84rem; font-weight: 600; color: var(--c-rose-dark);">Specify your spiritual tradition name: <span class="text-danger">*</span></label>
                            <input type="text" name="spiritual_path_other" id="pathOtherInput" placeholder="Enter name of your spiritual tradition or parampara" value="<?= e($savedPath) ?>">
                        </div>
                    </div>

                    <!-- 🌟 Spiritual Organization / Sangha & Guru -->
                    <div class="field mb-4">
                        <label>Spiritual Organization / Sangha <span style="opacity: .7;">(Recommended)</span></label>
                        <select name="spiritual_organization" id="spiritualOrgSelect" onchange="handleOrgChange(this.value)">
                            <option value="">Select spiritual organization / sangha...</option>
                            <?php foreach (spiritual_organizations() as $org): ?>
                                <option value="<?= e($org) ?>" <?= $savedOrg === $org ? 'selected' : '' ?>><?= e($org) ?></option>
                            <?php endforeach; ?>
                            <option value="Self-Guided / Independent" <?= $savedOrg === 'Self-Guided / Independent' ? 'selected' : '' ?>>Self-Guided / Independent Sangha</option>
                            <option value="Other" <?= (!empty($savedOrg) && !in_array($savedOrg, spiritual_organizations(), true) && $savedOrg !== 'Self-Guided / Independent') || $savedOrg === 'Other' ? 'selected' : '' ?>>✏️ Other (Specify name)</option>
                        </select>

                        <div class="quick-chips mt-2">
                            <span style="font-size: 0.8rem; font-weight: 600; color: #8A5A00; display: inline-flex; align-items: center;">Popular:</span>
                            <span class="quick-chip" onclick="selectSpiritualOrg('ISKCON (International Society for Krishna Consciousness)')">ISKCON</span>
                            <span class="quick-chip" onclick="selectSpiritualOrg('The Art of Living Foundation')">Art of Living</span>
                            <span class="quick-chip" onclick="selectSpiritualOrg('Isha Foundation')">Isha Foundation</span>
                            <span class="quick-chip" onclick="selectSpiritualOrg('Ramakrishna Math and Ramakrishna Mission')">Ramakrishna Math</span>
                            <span class="quick-chip" onclick="selectSpiritualOrg('BAPS Swaminarayan Sanstha')">BAPS Swaminarayan</span>
                            <span class="quick-chip" onclick="selectSpiritualOrg('Self-Guided / Independent')">Self-Guided</span>
                        </div>

                        <div id="orgOtherWrap" class="mt-2" style="<?= (!empty($savedOrg) && !in_array($savedOrg, spiritual_organizations(), true) && $savedOrg !== 'Self-Guided / Independent') || $savedOrg === 'Other' ? 'display:block;' : 'display:none;' ?>">
                            <input type="text" name="spiritual_organization_other" id="orgOtherInput" placeholder="Enter name of your organization / ashram / temple" value="<?= e($savedOrg) ?>">
                        </div>
                    </div>

                    <div class="field mb-4">
                        <label>Guru / Mentor / Spiritual Guide <span style="opacity: .7;">(Optional)</span></label>
                        <input type="text" name="guru" id="guruInput" placeholder="e.g. Diksha Guru, Shiksha Guru, Mentor name, or Ashram center" value="<?= e($spiritual['guru'] ?? '') ?>">
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
                            <select name="country" id="countrySelect" required onchange="handleCountryChange(this.value)">
                                <?php foreach (['India','United States','United Kingdom','Canada','Australia','United Arab Emirates','Singapore','Germany','New Zealand','Other'] as $c): ?>
                                    <option value="<?= $c ?>" <?= ($profile['country'] ?? 'India') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label>State / Province <span class="text-danger">*</span></label>
                            <!-- Indian States dropdown (active when country is India) -->
                            <div id="stateIndiaWrap" style="<?= $isIndia ? 'display:block;' : 'display:none;' ?>">
                                <select name="state" id="stateSelect">
                                    <option value="">Select State / UT...</option>
                                    <?php foreach (indian_states() as $st): ?>
                                        <option value="<?= e($st) ?>" <?= (strcasecmp($savedState, $st) === 0) ? 'selected' : '' ?>><?= e($st) ?></option>
                                    <?php endforeach; ?>
                                    <option value="Other" <?= (!empty($savedState) && !in_array($savedState, indian_states(), true)) ? 'selected' : '' ?>>Other / Outside India</option>
                                </select>
                            </div>
                            <!-- Other Country state text input -->
                            <div id="stateOtherWrap" style="<?= (!$isIndia || (!empty($savedState) && !in_array($savedState, indian_states(), true))) ? 'display:block;' : 'display:none;' ?>">
                                <input type="text" name="state_other" id="stateOtherInput" placeholder="e.g. California, Ontario, Greater London" value="<?= e($savedState) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="field mb-3">
                        <label>City <span class="text-danger">*</span></label>
                        <input type="text" name="city" id="cityInput" list="citySuggestions" required placeholder="e.g. Mumbai, Pune, Bangalore, Delhi NCR, Ahmedabad" value="<?= e($profile['city'] ?? '') ?>">
                        <datalist id="citySuggestions">
                            <option value="Mumbai">
                            <option value="Pune">
                            <option value="Bengaluru / Bangalore">
                            <option value="Delhi / New Delhi">
                            <option value="Noida / Greater Noida">
                            <option value="Gurugram / Gurgaon">
                            <option value="Hyderabad">
                            <option value="Chennai">
                            <option value="Kolkata">
                            <option value="Ahmedabad">
                            <option value="Surat">
                            <option value="Jaipur">
                            <option value="Lucknow">
                            <option value="Kanpur">
                            <option value="Nagpur">
                            <option value="Indore">
                            <option value="Thane">
                            <option value="Bhopal">
                            <option value="Visakhapatnam">
                            <option value="Vadodara">
                            <option value="Ghaziabad">
                            <option value="Ludhiana">
                            <option value="Agra">
                            <option value="Nashik">
                            <option value="Faridabad">
                            <option value="Meerut">
                            <option value="Rajkot">
                            <option value="Varanasi">
                            <option value="Srinagar">
                            <option value="Aurangabad / Chhatrapati Sambhajinagar">
                            <option value="Amritsar">
                            <option value="Navi Mumbai">
                            <option value="Prayagraj / Allahabad">
                            <option value="Ranchi">
                            <option value="Howrah">
                            <option value="Coimbatore">
                            <option value="Jabalpur">
                            <option value="Gwalior">
                            <option value="Vijayawada">
                            <option value="Jodhpur">
                            <option value="Madurai">
                            <option value="Raipur">
                            <option value="Kota">
                            <option value="Chandigarh">
                            <option value="Rishikesh">
                            <option value="Haridwar">
                            <option value="Vrindavan / Mathura">
                            <option value="Mayapur / Nabadwip">
                            <option value="Dubai / Abu Dhabi (UAE)">
                            <option value="London (UK)">
                            <option value="San Francisco / Bay Area (USA)">
                            <option value="New York / New Jersey (USA)">
                            <option value="Toronto (Canada)">
                            <option value="Singapore">
                            <option value="Sydney / Melbourne (Australia)">
                        </datalist>
                    </div>

                    <!-- Education & Profession -->
                    <div class="form-grid mb-3">
                        <div class="field">
                            <label>Highest Education <span class="text-danger">*</span></label>
                            <select name="education" required>
                                <option value="">Select education...</option>
                                <optgroup label="Post Graduate / Doctorate">
                                    <?php foreach (['Masters (M.Tech / M.S / M.Sc / M.A)','MBA / PGDM','Doctorate / Ph.D','CA / CS / CFA','MBBS / MD / Dental','Law (LLM / LLB)'] as $edu): ?>
                                        <option value="<?= $edu ?>" <?= ($profile['education'] ?? '') === $edu ? 'selected' : '' ?>><?= $edu ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Graduate / Bachelors">
                                    <?php foreach (['Bachelors (B.Tech / B.E / B.Sc / B.Com / B.A)','BCA / BBA','B.Arch','B.Pharm / Nursing'] as $edu): ?>
                                        <option value="<?= $edu ?>" <?= ($profile['education'] ?? '') === $edu ? 'selected' : '' ?>><?= $edu ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Other">
                                    <?php foreach (['Diploma','High School / Intermediate','Other'] as $edu): ?>
                                        <option value="<?= $edu ?>" <?= ($profile['education'] ?? '') === $edu ? 'selected' : '' ?>><?= $edu ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>

                        <div class="field">
                            <label>Occupation / Profession <span class="text-danger">*</span></label>
                            <input type="text" name="profession" id="professionInput" list="professionSuggestions" required placeholder="e.g. Software Engineer, Doctor, Business Owner, Teacher" value="<?= e($profile['profession'] ?? '') ?>">
                            <datalist id="professionSuggestions">
                                <option value="Software Engineer / IT Professional">
                                <option value="Data Scientist / AI Engineer">
                                <option value="Doctor / Physician / Surgeon">
                                <option value="Chartered Accountant (CA) / Finance">
                                <option value="Business Owner / Entrepreneur">
                                <option value="Civil Services / IAS / IPS / Govt Officer">
                                <option value="Professor / Teacher / Educator">
                                <option value="Banking / Financial Analyst">
                                <option value="Architect / Interior Designer">
                                <option value="Lawyer / Legal Consultant">
                                <option value="Marketing / Advertising / PR">
                                <option value="Human Resources (HR) Professional">
                                <option value="Civil / Mechanical / Electrical Engineer">
                                <option value="Yoga / Ayurveda / Wellness Practitioner">
                                <option value="Full-time Sevak / Spiritual Mission">
                                <option value="Scientific Researcher">
                                <option value="Management Consultant">
                                <option value="Artist / Designer / Content Creator">
                                <option value="Defense / Armed Forces">
                                <option value="Student">
                                <option value="Homemaker">
                                <option value="Other">
                            </datalist>
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

function selectSpiritualPath(path) {
    const sel = document.getElementById('spiritualPathSelect');
    if (sel) {
        let found = false;
        for (let opt of sel.options) {
            if (opt.value === path) {
                sel.value = path;
                found = true;
                break;
            }
        }
        if (!found) {
            sel.value = 'Other';
            const otherInput = document.getElementById('pathOtherInput');
            if (otherInput) otherInput.value = path;
        }
        handlePathChange(sel.value);
    }
}

function handlePathChange(val) {
    const wrap = document.getElementById('pathOtherWrap');
    if (wrap) {
        if (val === 'Other') {
            wrap.style.display = 'block';
            const inp = document.getElementById('pathOtherInput');
            if (inp) inp.focus();
        } else {
            wrap.style.display = 'none';
        }
    }
}

function selectSpiritualOrg(org) {
    const sel = document.getElementById('spiritualOrgSelect');
    if (sel) {
        let found = false;
        for (let opt of sel.options) {
            if (opt.value === org) {
                sel.value = org;
                found = true;
                break;
            }
        }
        if (!found) {
            sel.value = 'Other';
            const otherInp = document.getElementById('orgOtherInput');
            if (otherInp) otherInp.value = org;
        }
        handleOrgChange(sel.value);
    }
}

function handleOrgChange(val) {
    const wrap = document.getElementById('orgOtherWrap');
    if (wrap) {
        if (val === 'Other') {
            wrap.style.display = 'block';
            const inp = document.getElementById('orgOtherInput');
            if (inp) inp.focus();
        } else {
            wrap.style.display = 'none';
        }
    }
}

function handleCountryChange(val) {
    const indiaWrap = document.getElementById('stateIndiaWrap');
    const otherWrap = document.getElementById('stateOtherWrap');
    const stateSelect = document.getElementById('stateSelect');
    const stateOther = document.getElementById('stateOtherInput');

    if (val === 'India') {
        if (indiaWrap) indiaWrap.style.display = 'block';
        if (otherWrap) otherWrap.style.display = 'none';
        if (stateSelect) stateSelect.setAttribute('required', 'required');
        if (stateOther) stateOther.removeAttribute('required');
    } else {
        if (indiaWrap) indiaWrap.style.display = 'none';
        if (otherWrap) otherWrap.style.display = 'block';
        if (stateSelect) stateSelect.removeAttribute('required');
        if (stateOther) stateOther.setAttribute('required', 'required');
    }
}

// Watch for "Other" in state select dropdown
document.addEventListener('DOMContentLoaded', function() {
    const stateSel = document.getElementById('stateSelect');
    if (stateSel) {
        stateSel.addEventListener('change', function() {
            const otherWrap = document.getElementById('stateOtherWrap');
            if (this.value === 'Other') {
                if (otherWrap) otherWrap.style.display = 'block';
                const inp = document.getElementById('stateOtherInput');
                if (inp) inp.focus();
            } else {
                if (otherWrap && document.getElementById('countrySelect')?.value === 'India') {
                    otherWrap.style.display = 'none';
                }
            }
        });
    }
});

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
            // Ignore inputs inside hidden containers
            if (input.offsetParent === null) {
                continue;
            }

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
