<?php
/** @var array $u, $sp, $photos, $viewerPlan; @var array|null $interest; @var bool $shortlisted, $canMessage, $isComplete, $contactUnlocked, $targetFeatured, $targetBoosted; @var int|null $contactsLeft; @var string|null $targetBadge */
$age = age_from_dob($u['dob']);
$canUnlockContact = plan_can($viewerPlan, 'view_contacts') && ($contactsLeft === null || $contactsLeft > 0);
?>
<section class="section-tight">
<div class="container">
    <div class="flex-between mb-3" style="flex-wrap: wrap; gap: .5rem;">
        <a href="/browse" class="btn btn-ghost btn-sm">Back to browse</a>
        <div class="flex gap-1" style="flex-wrap: wrap;">
            <a href="/browse" class="btn btn-ghost btn-sm">Browse Profiles</a>
            <a href="/dashboard" class="btn btn-ghost btn-sm">Dashboard</a>
        </div>
    </div>

    <div class="profile-hero">
        <?php if ($photos):
            $primary = array_filter($photos, fn($p) => $p['is_primary']);
            $primary = $primary ? array_values($primary)[0] : $photos[0]; ?>
            <img src="<?= e(upload_url($primary['path'])) ?>" alt="">
        <?php else: ?>
            <img src="<?= e(avatar_url(['id' => $u['user_id'] ?? $u['id'], 'name' => $u['name']])) ?>" alt="">
        <?php endif; ?>

        <div>
            <div class="name-row">
                <h1><?= e($u['name']) ?></h1>
                <span class="age"><?= $age ? ($age . ' years') : '' ?></span>
            </div>
            <p style="color: var(--c-ink-soft); font-size: 1.05rem;">
                <?= e($u['profession'] ?: 'Seeker') ?> -
                <?= e(trim(($u['city'] ?? '') . ', ' . ($u['state'] ?? ''), ', ')) ?> -
                <?= cm_to_feet((int)($u['height_cm'] ?? 0)) ?>
            </p>
            <div class="profile-status-row">
                <?php if ($targetBadge): ?><span class="pill gold"><?= e($targetBadge) ?></span><?php endif; ?>
                <?= verified_badge($u['verified_tier'] ?? null) ?>
                <?php if ($targetBoosted): ?><span class="pill green">Boosted</span><?php endif; ?>
                <?php if ($targetFeatured): ?><span class="pill gold">Featured</span><?php endif; ?>
                <?php if (!$isComplete): ?><span class="pill gold">Profile in progress</span><?php endif; ?>
            </div>
            <?php if ($sp && $sp['spiritual_path']): ?>
                <div style="margin-top: .5rem;">
                    <span class="tag tag-gold" style="font-size: .85rem;"><?= e($sp['spiritual_path']) ?></span>
                    <?php if ($sp['guru']): ?><span class="tag" style="font-size: .85rem;">Guru: <?= e($sp['guru']) ?></span><?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="actions">
                <?php if ($canMessage): ?>
                    <a href="/messages/<?= (int)$u['id'] ?>" class="btn btn-primary">Send a Message</a>
                <?php elseif ($interest && $interest['status'] === 'sent' && (int)$interest['receiver_id'] === Auth::id()): ?>
                    <form method="post" action="/interest/<?= (int)$interest['id'] ?>/accept" style="display:inline;">
                        <?= csrf_field() ?><button class="btn btn-primary">Accept Interest</button>
                    </form>
                    <form method="post" action="/interest/<?= (int)$interest['id'] ?>/decline" style="display:inline;">
                        <?= csrf_field() ?><button class="btn btn-ghost">Decline</button>
                    </form>
                <?php elseif (!$interest || $interest['status'] === 'cancelled' || $interest['status'] === 'declined'): ?>
                    <form method="post" action="/interest/send/<?= (int)$u['id'] ?>" style="display:inline;">
                        <?= csrf_field() ?><button class="btn btn-primary">Express Interest</button>
                    </form>
                <?php elseif ($interest['status'] === 'sent'): ?>
                    <span class="pill gold" style="padding: .8rem 1.4rem;">Interest sent - awaiting response</span>
                <?php endif; ?>

                <form method="post" action="/shortlist/<?= (int)$u['id'] ?>" style="display:inline;">
                    <?= csrf_field() ?><button class="btn btn-ghost"><?= $shortlisted ? 'Shortlisted' : 'Shortlist' ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="info-grid mb-4">
        <div class="info-card">
            <h3>Contact Details</h3>
            <?php if ($contactUnlocked): ?>
                <div class="info-row"><span class="k">Phone</span><span class="v"><?= e($u['phone'] ?: '-') ?></span></div>
                <div class="info-row"><span class="k">Email</span><span class="v"><?= e($u['email'] ?: '-') ?></span></div>
                <p style="margin-top: 1rem; color: var(--c-muted);">This contact unlock is saved for your current membership period.</p>
            <?php elseif ($canUnlockContact): ?>
                <p>Unlock phone and email details using your membership contact quota.</p>
                <p style="color: var(--c-muted);">Contacts remaining: <?= $contactsLeft === null ? 'Unlimited' : (int)$contactsLeft ?></p>
                <form method="post" action="/member/<?= (int)$u['id'] ?>/unlock-contact">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary">Unlock Contact Details</button>
                </form>
            <?php else: ?>
                <p>Contact details are a premium feature.</p>
                <p style="color: var(--c-muted);">
                    <?= plan_can($viewerPlan, 'view_contacts')
                        ? 'You have reached your contact view limit for this membership.'
                        : 'Upgrade to Starter Premium or higher to view contact details.' ?>
                </p>
                <a href="/packages" class="btn btn-gold">Upgrade Plan</a>
            <?php endif; ?>
        </div>

        <div class="info-card">
            <h3>Membership Fit</h3>
            <div class="info-row"><span class="k">Your Plan</span><span class="v"><?= e($viewerPlan['name']) ?></span></div>
            <div class="info-row"><span class="k">Contact Limit</span><span class="v"><?= (float)$viewerPlan['price'] <= 0 ? '0' : ((int)$viewerPlan['contacts_limit'] === 0 ? 'Unlimited' : (int)$viewerPlan['contacts_limit']) ?></span></div>
            <div class="info-row"><span class="k">Search Priority</span><span class="v"><?= e(plan_priority_label($viewerPlan)) ?></span></div>
            <div class="info-row"><span class="k">Advanced Search</span><span class="v"><?= plan_can($viewerPlan, 'advanced_search') ? 'Yes' : 'No' ?></span></div>
        </div>
    </div>

    <?php if (!empty($u['about_me'])): ?>
    <div class="admin-card mb-4">
        <h3 style="color: var(--c-maroon);">About <?= e(explode(' ', $u['name'])[0]) ?></h3>
        <p style="font-size: 1.05rem; line-height: 1.7;"><?= nl2br(e($u['about_me'])) ?></p>
        <?php if (!empty($u['partner_pref'])): ?>
            <h3 class="mt-3" style="color: var(--c-maroon);">Looking for</h3>
            <p style="font-size: 1.05rem; line-height: 1.7;"><?= nl2br(e($u['partner_pref'])) ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="info-grid">
        <div class="info-card">
            <h3>Basic Details</h3>
            <div class="info-row"><span class="k">Date of Birth</span><span class="v"><?= e($u['dob']) ?></span></div>
            <div class="info-row"><span class="k">Marital Status</span><span class="v"><?= e(ucfirst(str_replace('_',' ', $u['marital_status'] ?? ''))) ?></span></div>
            <div class="info-row"><span class="k">Religion</span><span class="v"><?= e($u['religion'] ?? '-') ?></span></div>
            <div class="info-row"><span class="k">Community</span><span class="v"><?= e($u['community'] ?? '-') ?></span></div>
            <div class="info-row"><span class="k">Mother Tongue</span><span class="v"><?= e($u['mother_tongue'] ?? '-') ?></span></div>
            <div class="info-row"><span class="k">Diet</span><span class="v"><?= e(ucfirst(str_replace('_',' ', $u['diet'] ?? '-'))) ?></span></div>
        </div>

        <div class="info-card">
            <h3>Education &amp; Career</h3>
            <div class="info-row"><span class="k">Education</span><span class="v"><?= e($u['education'] ?? '-') ?></span></div>
            <div class="info-row"><span class="k">Profession</span><span class="v"><?= e($u['profession'] ?? '-') ?></span></div>
            <div class="info-row"><span class="k">Income</span><span class="v"><?= e($u['annual_income'] ?? '-') ?></span></div>
            <div class="info-row"><span class="k">Location</span><span class="v"><?= e(trim(($u['city'] ?? '') . ', ' . ($u['state'] ?? '') . ', ' . ($u['country'] ?? ''), ', ')) ?></span></div>
            <div class="info-row"><span class="k">Family Type</span><span class="v"><?= e(ucfirst($u['family_type'] ?? '-')) ?></span></div>
        </div>

        <?php if ($sp): ?>
        <div class="info-card" style="grid-column: 1 / -1;">
            <h3>Spiritual Profile</h3>
            <div class="info-grid">
                <div>
                    <div class="info-row"><span class="k">Path</span><span class="v"><?= e($sp['spiritual_path'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="k">Guru</span><span class="v"><?= e($sp['guru'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="k">Ishta Devata</span><span class="v"><?= e($sp['ishta_devata'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="k">Mantra</span><span class="v"><?= e($sp['mantra'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="k">Organization</span><span class="v"><?= e($sp['spiritual_organization'] ?? '-') ?></span></div>
                </div>
                <div>
                    <div class="info-row"><span class="k">Daily Sadhana</span><span class="v"><?= e($sp['daily_sadhana'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="k">Favorite Scripture</span><span class="v"><?= e($sp['favorite_scripture'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="k">Fasting</span><span class="v"><?= e($sp['fasting_practice'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="k">Lifestyle</span><span class="v"><?= e($sp['spiritual_lifestyle'] ?? '-') ?></span></div>
                </div>
            </div>
            <?php if (!empty($sp['pilgrimage_done'])): ?>
                <p style="margin-top: 1rem; padding: 1rem; background: var(--c-cream-2); border-radius: var(--r); font-style: italic;">
                    <strong>Pilgrimages:</strong> <?= e($sp['pilgrimage_done']) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="admin-card text-center mt-4" style="padding: 2rem;">
        <p style="font-family: var(--f-display); font-size: 1.3rem; margin: 0 0 .4rem; color: var(--c-maroon);">Keep exploring</p>
        <p style="color: var(--c-muted); margin: 0 0 1rem;">Every seeker is unique. Review more profiles that share your path.</p>
        <div class="flex gap-1" style="justify-content: center; flex-wrap: wrap;">
            <a href="/browse" class="btn btn-primary">See New Profiles</a>
            <a href="/dashboard" class="btn btn-ghost">Dashboard</a>
            <a href="/shortlist" class="btn btn-ghost">My Shortlist</a>
            <a href="/interests" class="btn btn-ghost">My Interests</a>
        </div>
    </div>
</div>
</section>
