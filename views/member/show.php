<?php /** @var array $u, $sp, $photos; @var array|null $interest; @var bool $shortlisted, $viewerComplete, $compatible, $canMessage */ $age = age_from_dob($u['dob']); ?>
<section class="section-tight">
<div class="container">
    <a href="/browse" class="btn btn-ghost btn-sm mb-3">← Back to browse</a>

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
                <?= e($u['profession'] ?: 'Seeker') ?> ·
                <?= e(trim(($u['city'] ?? '') . ', ' . ($u['state'] ?? ''), ', ')) ?> ·
                <?= cm_to_feet((int)($u['height_cm'] ?? 0)) ?>
            </p>
            <?php if ($sp && $sp['spiritual_path']): ?>
                <div style="margin-top: .5rem;">
                    <span class="tag tag-gold" style="font-size: .85rem;"><?= e($sp['spiritual_path']) ?></span>
                    <?php if ($sp['guru']): ?><span class="tag" style="font-size: .85rem;">Guru: <?= e($sp['guru']) ?></span><?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="actions">
                <?php if (isset($viewerComplete, $compatible, $canMessage)): ?>
                    <?php if (!$viewerComplete): ?>
                        <a href="/profile/edit" class="btn btn-primary">Complete Profile</a>
                    <?php elseif (!$compatible): ?>
                        <span class="pill gold" style="padding: .8rem 1.4rem;">Not a compatible match</span>
                    <?php elseif ($canMessage): ?>
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
                <?php else: ?>
                <?php if (!$interest || $interest['status'] === 'cancelled' || $interest['status'] === 'declined'): ?>
                    <form method="post" action="/interest/send/<?= (int)$u['id'] ?>" style="display:inline;">
                        <?= csrf_field() ?><button class="btn btn-primary">💌 Express Interest</button>
                    </form>
                <?php elseif ($interest['status'] === 'sent'): ?>
                    <span class="pill gold" style="padding: .8rem 1.4rem;">Interest sent · awaiting response</span>
                <?php elseif ($interest['status'] === 'accepted'): ?>
                    <a href="/messages/<?= (int)$u['id'] ?>" class="btn btn-primary">💬 Send a Message</a>
                <?php endif; ?>
                <?php endif; ?>

                <form method="post" action="/shortlist/<?= (int)$u['id'] ?>" style="display:inline;">
                    <?= csrf_field() ?><button class="btn btn-ghost"><?= $shortlisted ? '⭐ Shortlisted' : '☆ Shortlist' ?></button>
                </form>
            </div>
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
            <div class="info-row"><span class="k">Religion</span><span class="v"><?= e($u['religion'] ?? '—') ?></span></div>
            <div class="info-row"><span class="k">Community</span><span class="v"><?= e($u['community'] ?? '—') ?></span></div>
            <div class="info-row"><span class="k">Mother Tongue</span><span class="v"><?= e($u['mother_tongue'] ?? '—') ?></span></div>
            <div class="info-row"><span class="k">Diet</span><span class="v"><?= e(ucfirst(str_replace('_',' ', $u['diet'] ?? '—'))) ?></span></div>
        </div>

        <div class="info-card">
            <h3>Education &amp; Career</h3>
            <div class="info-row"><span class="k">Education</span><span class="v"><?= e($u['education'] ?? '—') ?></span></div>
            <div class="info-row"><span class="k">Profession</span><span class="v"><?= e($u['profession'] ?? '—') ?></span></div>
            <div class="info-row"><span class="k">Income</span><span class="v"><?= e($u['annual_income'] ?? '—') ?></span></div>
            <div class="info-row"><span class="k">Location</span><span class="v"><?= e(trim(($u['city'] ?? '') . ', ' . ($u['state'] ?? '') . ', ' . ($u['country'] ?? ''), ', ')) ?></span></div>
            <div class="info-row"><span class="k">Family Type</span><span class="v"><?= e(ucfirst($u['family_type'] ?? '—')) ?></span></div>
        </div>

        <?php if ($sp): ?>
        <div class="info-card" style="grid-column: 1 / -1;">
            <h3>🕉️ Spiritual Profile</h3>
            <div class="info-grid">
                <div>
                    <div class="info-row"><span class="k">Path</span><span class="v"><?= e($sp['spiritual_path'] ?? '—') ?></span></div>
                    <div class="info-row"><span class="k">Guru</span><span class="v"><?= e($sp['guru'] ?? '—') ?></span></div>
                    <div class="info-row"><span class="k">Ishta Devata</span><span class="v"><?= e($sp['ishta_devata'] ?? '—') ?></span></div>
                    <div class="info-row"><span class="k">Mantra</span><span class="v"><?= e($sp['mantra'] ?? '—') ?></span></div>
                </div>
                <div>
                    <div class="info-row"><span class="k">Daily Sadhana</span><span class="v"><?= e($sp['daily_sadhana'] ?? '—') ?></span></div>
                    <div class="info-row"><span class="k">Favorite Scripture</span><span class="v"><?= e($sp['favorite_scripture'] ?? '—') ?></span></div>
                    <div class="info-row"><span class="k">Fasting</span><span class="v"><?= e($sp['fasting_practice'] ?? '—') ?></span></div>
                </div>
            </div>
            <?php if (!empty($sp['pilgrimage_done'])): ?>
                <p style="margin-top: 1rem; padding: 1rem; background: var(--c-cream-2); border-radius: var(--r); font-style: italic;">
                    <strong>Pilgrimages:</strong> <?= e($sp['pilgrimage_done']) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</section>
