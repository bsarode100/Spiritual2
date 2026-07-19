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
            <div class="field"><label>Marital Status</label>
                <select name="marital_status">
                    <?php foreach (['never_married'=>'Never Married','divorced'=>'Divorced','widowed'=>'Widowed','separated'=>'Separated'] as $k=>$v): ?>
                        <option value="<?= $k ?>" <?= ($profile['marital_status'] ?? '')===$k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label>Mother Tongue</label><input type="text" name="mother_tongue" value="<?= e($profile['mother_tongue'] ?? '') ?>" placeholder="Hindi, Tamil, etc."></div>
            <div class="field"><label>Religion</label><input type="text" name="religion" value="<?= e($profile['religion'] ?? 'Hindu') ?>"></div>
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
        <div class="form-grid-3">
            <div class="field"><label>Country</label><input type="text" name="country" value="<?= e($profile['country'] ?? 'India') ?>"></div>
            <div class="field"><label>State</label><input type="text" name="state" value="<?= e($profile['state'] ?? '') ?>"></div>
            <div id="field-city" class="<?= $cls('city') ?>"><label>City <span style="color: var(--c-maroon);">*</span></label><input type="text" name="city" value="<?= e($profile['city'] ?? '') ?>" required></div>
        </div>

        <h3 class="mt-4">Education &amp; Career</h3>
        <div class="form-grid">
            <div class="field"><label>Education</label><input type="text" name="education" value="<?= e($profile['education'] ?? '') ?>"></div>
            <div class="field"><label>Profession</label><input type="text" name="profession" value="<?= e($profile['profession'] ?? '') ?>"></div>
            <div class="field"><label>Annual Income</label><input type="text" name="annual_income" value="<?= e($profile['annual_income'] ?? '') ?>" placeholder="e.g. 8-12 LPA"></div>
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
            <div class="field"><label>Spiritual Path</label><input type="text" name="spiritual_path" value="<?= e($spiritual['spiritual_path'] ?? '') ?>" placeholder="ISKCON / Vipassana / Sahaja Yoga / Art of Living..."></div>
            <div class="field"><label>Guru</label><input type="text" name="guru" value="<?= e($spiritual['guru'] ?? '') ?>"></div>
            <div class="field"><label>Ishta Devata</label><input type="text" name="ishta_devata" value="<?= e($spiritual['ishta_devata'] ?? '') ?>" placeholder="Krishna / Devi / Shiva..."></div>
            <div class="field"><label>Mantra</label><input type="text" name="mantra" value="<?= e($spiritual['mantra'] ?? '') ?>"></div>
            <div class="field full"><label>Daily Sadhana</label><input type="text" name="daily_sadhana" value="<?= e($spiritual['daily_sadhana'] ?? '') ?>" placeholder="108 mala japa, 1hr meditation, etc."></div>
            <div class="field"><label>Favorite Scripture</label><input type="text" name="favorite_scripture" value="<?= e($spiritual['favorite_scripture'] ?? '') ?>"></div>
            <div class="field"><label>Fasting Practice</label><input type="text" name="fasting_practice" value="<?= e($spiritual['fasting_practice'] ?? '') ?>"></div>
            <div class="field"><label>Spiritual Organization</label><input type="text" name="spiritual_organization" value="<?= e($spiritual['spiritual_organization'] ?? '') ?>"></div>
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
            <div class="field full">
                <label>Spiritual Lifestyle Commitments</label>
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
