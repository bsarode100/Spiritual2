<?php
/** @var array|null $profile, $spiritual, $horoscope */
/** @var array $missing */
/** @var int $photoCount */
$me = Auth::user();
$missing = $missing ?? [];
$photoCount = $photoCount ?? 0;
// Helper for tagging a field as missing so CSS can outline it in red.
$cls = fn(string $key) => isset($missing[$key]) ? 'field field-error' : 'field';
?>
<section class="section-tight">
<div class="container">
    <div class="flex-between mb-4">
        <div>
            <span class="eyebrow">Your profile</span>
            <h1 style="margin: 0;">Edit your bio-data</h1>
        </div>
        <a href="/dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <?php if ($missing): ?>
        <?php $remaining = count($missing); ?>
        <div class="profile-missing-banner mb-3">
            <strong>
                <?= $remaining === 1
                    ? 'One more thing to unlock Express Interest.'
                    : $remaining . ' items to unlock Express Interest.' ?>
            </strong>
            Please complete the highlighted fields below:
            <ul>
                <?php foreach ($missing as $k => $label): ?>
                    <li>
                        <?php if ($k === 'photos'): ?>
                            <a href="/profile/photos"><?= e($label) ?> (you have <?= (int)$photoCount ?>)</a>
                        <?php else: ?>
                            <a href="#field-<?= e($k) ?>"><?= e($label) ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="profile-complete-banner mb-3">
            <strong>Your profile is complete.</strong> Other seekers can find you in browse and you can express interest freely.
        </div>
    <?php endif; ?>

    <p style="color: var(--c-muted); font-size: .95rem; margin-bottom: 1rem;">
        Fields marked with <span style="color: var(--c-maroon); font-weight: 700;">*</span>
        are required — you'll need them to view other seekers and express interest.
        You also need at least <?= PROFILE_PHOTO_MIN ?> profile photos
        (<a href="/profile/photos">manage photos</a> — <?= (int)$photoCount ?> of <?= PROFILE_PHOTO_MAX ?> uploaded).
    </p>

    <form method="post" action="/profile/edit" class="admin-card mb-4">
        <?= csrf_field() ?>
        <h3 id="basic">Basic Information</h3>
        <div class="form-grid">
            <div id="field-name" class="<?= $cls('name') ?>"><label>Full Name <span style="color: var(--c-maroon);">*</span></label><input type="text" name="name" value="<?= e($me['name']) ?>" required></div>
            <div id="field-dob" class="<?= $cls('dob') ?>"><label>Date of Birth <span style="color: var(--c-maroon);">*</span></label><input type="date" name="dob" value="<?= e($profile['dob'] ?? '') ?>" required></div>
            <div id="field-gender" class="<?= $cls('gender') ?>"><label>Gender <span style="color: var(--c-maroon);">*</span></label>
                <select name="gender" required>
                    <option value="female" <?= ($profile['gender'] ?? '')==='female' ? 'selected' : '' ?>>Female</option>
                    <option value="male" <?= ($profile['gender'] ?? '')==='male' ? 'selected' : '' ?>>Male</option>
                </select>
            </div>
            <div class="field"><label>Height (cm)</label><input type="number" name="height_cm" value="<?= e($profile['height_cm'] ?? '') ?>" min="120" max="220"></div>
            <div id="field-marital_status" class="<?= $cls('marital_status') ?>">
                <label>Marital Status <span style="color: var(--c-maroon);">*</span></label>
                <select name="marital_status" required>
                    <option value="">Select marital status...</option>
                    <?php foreach (['never_married'=>'Never Married','divorced'=>'Divorced','widowed'=>'Widowed','separated'=>'Separated'] as $k=>$v): ?>
                        <option value="<?= $k ?>" <?= ($profile['marital_status'] ?? '')===$k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label>Mother Tongue</label><input type="text" name="mother_tongue" value="<?= e($profile['mother_tongue'] ?? '') ?>" placeholder="Hindi, Tamil, etc."></div>
            <div id="field-religion" class="<?= $cls('religion') ?>">
                <label>Religion <span style="color: var(--c-maroon);">*</span></label>
                <input type="text" name="religion" value="<?= e($profile['religion'] ?? 'Hindu') ?>" placeholder="e.g. Hindu, Jain, Buddhist, Sikh..." required>
            </div>
            <div class="field"><label>Community</label><input type="text" name="community" value="<?= e($profile['community'] ?? '') ?>" placeholder="Brahmin, Vaishnav, etc."></div>
            <div class="field"><label>Caste</label><input type="text" name="caste" value="<?= e($profile['caste'] ?? '') ?>"></div>
            <div class="field"><label>Gotra</label><input type="text" name="gotra" value="<?= e($profile['gotra'] ?? '') ?>"></div>
            <div class="field"><label>Manglik</label>
                <select name="manglik">
                    <option value="dont_know" <?= ($profile['manglik'] ?? '')==='dont_know' ? 'selected' : '' ?>>Don't know</option>
                    <option value="no" <?= ($profile['manglik'] ?? '')==='no' ? 'selected' : '' ?>>No</option>
                    <option value="yes" <?= ($profile['manglik'] ?? '')==='yes' ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>
            <div class="field"><label>Diet</label>
                <select name="diet">
                    <?php foreach (['vegetarian','sattvic','vegan','eggetarian','non_vegetarian','jain'] as $d): ?>
                        <option value="<?= $d ?>" <?= ($profile['diet'] ?? '')===$d ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ', $d)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h3 class="mt-4">Location</h3>
        <?php
        $savedCountry = trim((string)($profile['country'] ?? 'India'));
        $savedState   = trim((string)($profile['state'] ?? ''));
        $indianStates = indian_states();
        $isIndia      = ($savedCountry === '' || strcasecmp($savedCountry, 'India') === 0);
        $isOtherCountry = !$isIndia;
        ?>
        <div class="form-grid-3 location-grid">
            <!-- Country Selector -->
            <div id="field-country" class="<?= $cls('country') ?>">
                <label for="country_select">Country <span style="color: var(--c-maroon);">*</span></label>
                <select name="country" id="country_select" class="glass-control" required>
                    <option value="India" <?= $isIndia ? 'selected' : '' ?>>India</option>
                    <option value="Other" <?= $isOtherCountry ? 'selected' : '' ?>>Other (International / NRI)</option>
                </select>
                <!-- Manual input if Other country is chosen -->
                <div id="country_other_wrap" class="mt-2" style="<?= $isOtherCountry ? 'display:block;' : 'display:none;' ?>">
                    <input type="text" name="country_other" id="country_other_input" value="<?= $isOtherCountry ? e($savedCountry) : '' ?>" placeholder="Specify country (e.g. United States, UK, Canada...)" class="glass-control">
                </div>
            </div>

            <!-- State Field (Dropdown for India, Manual for Other) -->
            <div id="field-state" class="<?= $cls('state') ?>">
                <label for="state_india_select">State / Province <span style="color: var(--c-maroon);">*</span></label>
                <!-- Indian States Dropdown -->
                <div id="state_india_wrap" style="<?= $isIndia ? 'display:block;' : 'display:none;' ?>">
                    <select name="state_india" id="state_india_select" class="glass-control">
                        <option value="">Select State / UT</option>
                        <?php foreach ($indianStates as $st): ?>
                            <option value="<?= e($st) ?>" <?= (strcasecmp($savedState, $st) === 0) ? 'selected' : '' ?>><?= e($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Other State Manual Input -->
                <div id="state_other_wrap" style="<?= $isOtherCountry ? 'display:block;' : 'display:none;' ?>">
                    <input type="text" name="state_other" id="state_other_input" value="<?= $isOtherCountry ? e($savedState) : '' ?>" placeholder="e.g. California, Ontario, Greater London..." class="glass-control">
                </div>
            </div>

            <!-- City Field (Manual entry in both cases) -->
            <div id="field-city" class="<?= $cls('city') ?>">
                <label>City / Town <span style="color: var(--c-maroon);">*</span></label>
                <input type="text" name="city" value="<?= e($profile['city'] ?? '') ?>" placeholder="e.g. Pune, Mumbai, Rishikesh..." required>
            </div>
        </div>

        <h3 class="mt-4">Education &amp; Career</h3>
        <div class="form-grid">
            <div class="field"><label>Education</label><input type="text" name="education" value="<?= e($profile['education'] ?? '') ?>"></div>
            <div id="field-profession" class="<?= $cls('profession') ?>">
                <label>Profession <span style="color: var(--c-maroon);">*</span></label>
                <input type="text" name="profession" value="<?= e($profile['profession'] ?? '') ?>" placeholder="e.g. Software Engineer, Yoga Teacher, Doctor, Seeker..." required>
            </div>
            <div id="field-annual_income" class="<?= $cls('annual_income') ?>">
                <label>Annual Income <span style="color: var(--c-maroon);">*</span></label>
                <input type="text" name="annual_income" value="<?= e($profile['annual_income'] ?? '') ?>" placeholder="e.g. 8-12 LPA, 15-20 LPA, 50k+ USD..." required>
            </div>
            <div class="field"><label>Family Type</label>
                <select name="family_type">
                    <option value="nuclear" <?= ($profile['family_type'] ?? '')==='nuclear' ? 'selected' : '' ?>>Nuclear</option>
                    <option value="joint" <?= ($profile['family_type'] ?? '')==='joint' ? 'selected' : '' ?>>Joint</option>
                    <option value="other" <?= ($profile['family_type'] ?? '')==='other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
        </div>

        <h3 class="mt-4">About</h3>
        <div id="field-about_me" class="<?= $cls('about_me') ?>"><label>About me <span style="color: var(--c-maroon);">*</span></label><textarea name="about_me" rows="5" required><?= e($profile['about_me'] ?? '') ?></textarea></div>
        <div class="field"><label>Partner preference</label><textarea name="partner_pref" rows="4"><?= e($profile['partner_pref'] ?? '') ?></textarea></div>

        <button class="btn btn-primary btn-lg">Save Profile</button>
    </form>

    <form method="post" action="/profile/spiritual" class="admin-card" id="spiritual">
        <?= csrf_field() ?>
        <h3>Spiritual Details</h3>
        <p style="color: var(--c-muted);">The heart of your profile. Share what others won't find on traditional matrimony sites.</p>
        <div class="form-grid">
            <?php
            $savedPath = trim((string)($spiritual['spiritual_path'] ?? ''));
            $pathList = spiritual_paths();
            $isPathPredefined = in_array($savedPath, $pathList, true);
            $isPathOther = ($savedPath !== '' && !$isPathPredefined);
            ?>
            <div id="field-spiritual_path" class="field full spiritual-path-container <?= $cls('spiritual_path') ?>">
                <label>Spiritual Path / Lineage <span style="color: var(--c-maroon);">*</span></label>
                <div class="custom-combobox" id="spiritual_path_combobox" data-other-wrap="spiritual_path_other_wrap" data-other-input="spiritual_path_other_input">
                    <input type="hidden" name="spiritual_path" id="spiritual_path_value" value="<?= $isPathOther ? 'Other' : e($savedPath) ?>">
                    
                    <div class="combobox-trigger" id="spiritual_path_trigger" tabindex="0" role="combobox" aria-expanded="false" aria-haspopup="listbox">
                        <span class="combobox-text <?= ($savedPath === '') ? 'is-placeholder' : '' ?>">
                            <?php if ($isPathOther): ?>
                                Other: <?= e($savedPath) ?>
                            <?php elseif ($isPathPredefined): ?>
                                <?= e($savedPath) ?>
                            <?php else: ?>
                                Select or search spiritual path...
                            <?php endif; ?>
                        </span>
                        <div class="combobox-icons">
                            <button type="button" class="combobox-clear" id="spiritual_path_clear" title="Clear selection" style="<?= ($savedPath !== '') ? 'display:inline-flex;' : 'display:none;' ?>">×</button>
                            <svg class="combobox-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                    </div>

                    <div class="combobox-dropdown" id="spiritual_path_dropdown" role="listbox">
                        <div class="combobox-search-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" class="combobox-search-input" id="spiritual_path_search" placeholder="Search 39+ spiritual paths..." autocomplete="off">
                        </div>
                        <div class="combobox-options-list" id="spiritual_path_options">
                            <?php foreach ($pathList as $p): ?>
                                <div class="combobox-option <?= ($savedPath === $p) ? 'is-selected' : '' ?>" data-value="<?= e($p) ?>" role="option">
                                    <?= e($p) ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="combobox-divider"></div>
                            <div class="combobox-option combobox-other-option <?= $isPathOther ? 'is-selected' : '' ?>" data-value="Other" role="option">
                                <span>✏️ <strong>Other</strong> (Not in list / Specify manually)</span>
                            </div>
                            <div class="combobox-empty" id="spiritual_path_empty" style="display: none;">
                                <div>No matching spiritual path found.</div>
                                <button type="button" class="btn btn-ghost btn-sm select-other-btn">Select "Other" &amp; Enter Name</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Manual Input when Other is chosen -->
                <div id="spiritual_path_other_wrap" class="mt-2" style="<?= $isPathOther ? 'display:block;' : 'display:none;' ?>">
                    <label for="spiritual_path_other_input" style="font-size: 0.84rem; color: var(--c-rose); font-weight: 600; display: block; margin-bottom: 0.35rem;">
                        Specify Your Spiritual Path / Tradition Name: <span style="color: var(--c-maroon);">*</span>
                    </label>
                    <input type="text" name="spiritual_path_other" id="spiritual_path_other_input" value="<?= $isPathOther ? e($savedPath) : '' ?>" placeholder="Enter the name of your spiritual path or tradition..." class="glass-control" style="background:#FFFFFF;">
                </div>
            </div>
            <div class="field"><label>Guru</label><input type="text" name="guru" value="<?= e($spiritual['guru'] ?? '') ?>"></div>
            <div id="field-ishta_devata" class="<?= $cls('ishta_devata') ?>"><label>Isht Devta <span style="color: var(--c-maroon);">*</span></label><input type="text" name="ishta_devata" value="<?= e($spiritual['ishta_devata'] ?? '') ?>" placeholder="Krishna / Devi / Shiva..." required></div>
            <div class="field"><label>Mantra</label><input type="text" name="mantra" value="<?= e($spiritual['mantra'] ?? '') ?>"></div>
            <div class="field full"><label>Daily Sadhana</label><input type="text" name="daily_sadhana" value="<?= e($spiritual['daily_sadhana'] ?? '') ?>" placeholder="108 mala japa, 1hr meditation, etc."></div>
            <div class="field"><label>Favorite Scripture</label><input type="text" name="favorite_scripture" value="<?= e($spiritual['favorite_scripture'] ?? '') ?>"></div>
            <div class="field"><label>Fasting Practice</label><input type="text" name="fasting_practice" value="<?= e($spiritual['fasting_practice'] ?? '') ?>"></div>
            <?php
            $savedOrg = trim((string)($spiritual['spiritual_organization'] ?? ''));
            $orgList = spiritual_organizations();
            $isPredefined = in_array($savedOrg, $orgList, true);
            $isOther = ($savedOrg !== '' && !$isPredefined);
            ?>
            <div class="field full spiritual-org-container">
                <label>Spiritual Organization / Sangha</label>
                <div class="custom-combobox" id="spiritual_org_combobox" data-other-wrap="spiritual_org_other_wrap" data-other-input="spiritual_org_other_input">
                    <input type="hidden" name="spiritual_organization" id="spiritual_org_value" value="<?= $isOther ? 'Other' : e($savedOrg) ?>">
                    
                    <div class="combobox-trigger" id="spiritual_org_trigger" tabindex="0" role="combobox" aria-expanded="false" aria-haspopup="listbox">
                        <span class="combobox-text <?= ($savedOrg === '') ? 'is-placeholder' : '' ?>">
                            <?php if ($isOther): ?>
                                Other: <?= e($savedOrg) ?>
                            <?php elseif ($isPredefined): ?>
                                <?= e($savedOrg) ?>
                            <?php else: ?>
                                Select or search organization...
                            <?php endif; ?>
                        </span>
                        <div class="combobox-icons">
                            <button type="button" class="combobox-clear" id="spiritual_org_clear" title="Clear selection" style="<?= ($savedOrg !== '') ? 'display:inline-flex;' : 'display:none;' ?>">×</button>
                            <svg class="combobox-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                    </div>

                    <div class="combobox-dropdown" id="spiritual_org_dropdown" role="listbox">
                        <div class="combobox-search-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" class="combobox-search-input" id="spiritual_org_search" placeholder="Search 40+ spiritual organizations..." autocomplete="off">
                        </div>
                        <div class="combobox-options-list" id="spiritual_org_options">
                            <?php foreach ($orgList as $o): ?>
                                <div class="combobox-option <?= ($savedOrg === $o) ? 'is-selected' : '' ?>" data-value="<?= e($o) ?>" role="option">
                                    <?= e($o) ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="combobox-divider"></div>
                            <div class="combobox-option combobox-other-option <?= $isOther ? 'is-selected' : '' ?>" data-value="Other" role="option">
                                <span>✏️ <strong>Other</strong> (Not in list / Specify manually)</span>
                            </div>
                            <div class="combobox-empty" id="spiritual_org_empty" style="display: none;">
                                <div>No matching organization found.</div>
                                <button type="button" class="btn btn-ghost btn-sm select-other-btn">Select "Other" &amp; Enter Name</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Manual Input when Other is chosen -->
                <div id="spiritual_org_other_wrap" class="mt-2" style="<?= $isOther ? 'display:block;' : 'display:none;' ?>">
                    <label for="spiritual_org_other_input" style="font-size: 0.84rem; color: var(--c-rose); font-weight: 600; display: block; margin-bottom: 0.35rem;">
                        Specify Your Spiritual Organization / Sangha Name:
                    </label>
                    <input type="text" name="spiritual_organization_other" id="spiritual_org_other_input" value="<?= $isOther ? e($savedOrg) : '' ?>" placeholder="Enter the name of your spiritual organization, center, or lineage..." class="glass-control" style="background:#FFFFFF;">
                </div>
            </div>
            <div class="field"><label>Temple Visit Frequency</label>
                <select name="temple_visit_frequency">
                    <option value="">Choose</option>
                    <?php foreach (['Daily','Weekly','Monthly','Occasionally'] as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= ($spiritual['temple_visit_frequency'] ?? '')===$opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label>Scripture Preference</label><input type="text" name="scripture_preference" value="<?= e($spiritual['scripture_preference'] ?? '') ?>"></div>
            <div class="field"><label>Festival Participation</label><input type="text" name="festival_participation" value="<?= e($spiritual['festival_participation'] ?? '') ?>"></div>
            <div class="field full"><label>Spiritual Lifestyle</label><input type="text" name="spiritual_lifestyle" value="<?= e($spiritual['spiritual_lifestyle'] ?? '') ?>" placeholder="Ashram seva, satsang, daily puja, meditation retreat..."></div>
            <div id="field-lifestyle_commitments" class="field full <?= $cls('lifestyle_commitments') ?>">
                <label>Spiritual Lifestyle Commitments <span style="color: var(--c-maroon);">*</span></label>
                <div class="admin-check-grid">
                    <label><input type="checkbox" name="vegetarian" value="1" <?= !empty($spiritual['vegetarian']) ? 'checked' : '' ?>> Vegetarian</label>
                    <label><input type="checkbox" name="vegan" value="1" <?= !empty($spiritual['vegan']) ? 'checked' : '' ?>> Vegan</label>
                    <label><input type="checkbox" name="no_smoking" value="1" <?= !empty($spiritual['no_smoking']) ? 'checked' : '' ?>> No smoking</label>
                    <label><input type="checkbox" name="no_alcohol" value="1" <?= !empty($spiritual['no_alcohol']) ? 'checked' : '' ?>> No alcohol</label>
                </div>
            </div>
            <div class="field full"><label>Pilgrimages Done</label><textarea name="pilgrimage_done" rows="2"><?= e($spiritual['pilgrimage_done'] ?? '') ?></textarea></div>
        </div>
        <button class="btn btn-primary btn-lg">Save Spiritual Details</button>
    </form>
</div>
</section>
