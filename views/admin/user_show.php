<?php /** @var array $u, $p, $sp */ ?>
<div class="admin-head">
    <h1><?= e($u['name']) ?></h1>
    <a href="/admin/users" class="btn btn-ghost btn-sm">← Back</a>
</div>

<div class="admin-card mb-3">
    <h3>Account</h3>
    <div class="info-row"><span class="k">Email</span><span class="v"><?= e($u['email']) ?></span></div>
    <div class="info-row"><span class="k">Phone</span><span class="v"><?= e($u['phone'] ?: '—') ?></span></div>
    <div class="info-row"><span class="k">Status</span><span class="v"><span class="pill <?= $u['status']==='active'?'green':'red' ?>"><?= e($u['status']) ?></span></span></div>
    <div class="info-row"><span class="k">Joined</span><span class="v"><?= date('F j, Y g:i a', strtotime($u['created_at'])) ?></span></div>
    <div class="info-row"><span class="k">Last Login</span><span class="v"><?= $u['last_login_at'] ? date('M j, Y g:i a', strtotime($u['last_login_at'])) : 'Never' ?></span></div>

    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--c-cream-2); display: flex; gap: .5rem; flex-wrap: wrap;">
        <form method="post" action="/admin/users/<?= (int)$u['id'] ?>/reset-link" style="margin: 0;">
            <?= csrf_field() ?>
            <button class="btn btn-ghost btn-sm">🔑 Generate password reset link</button>
        </form>
        <form method="post" action="/admin/users/<?= (int)$u['id'] ?>/toggle" style="margin: 0;">
            <?= csrf_field() ?>
            <button class="btn btn-ghost btn-sm"><?= $u['status']==='blocked' ? 'Unblock' : 'Block' ?></button>
        </form>
    </div>
</div>

<?php if (!empty($membership)): ?>
<div class="admin-card mb-3">
    <h3>Membership</h3>
    <div class="info-row"><span class="k">Current Plan</span><span class="v"><?= e($membership['plan']['name']) ?></span></div>
    <div class="info-row"><span class="k">Priority</span><span class="v"><?= e($membership['priority_label']) ?></span></div>
    <div class="info-row"><span class="k">Expiry</span><span class="v"><?= $membership['expires_at'] ? e(date('M j, Y', strtotime($membership['expires_at']))) : 'Lifetime / Free' ?></span></div>
    <div class="info-row"><span class="k">Days Remaining</span><span class="v"><?= $membership['days_left'] === null ? '-' : (int)$membership['days_left'] ?></span></div>
    <div class="info-row"><span class="k">Contacts Remaining</span><span class="v"><?= $membership['contacts_left'] === null ? 'Unlimited' : (int)$membership['contacts_left'] ?></span></div>
    <div class="info-row"><span class="k">Boosts Remaining</span><span class="v"><?= (int)$membership['boosts_left'] ?></span></div>
    <div style="margin-top: 1rem;">
        <a href="/admin/subscribers" class="btn btn-primary btn-sm">Manage Membership</a>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($verification) || (!empty($p) && ($p['verified_tier'] ?? 'none') !== 'none')): ?>
<div class="admin-card mb-3">
    <h3>Verification</h3>
    <div class="info-row"><span class="k">Badge</span><span class="v">
        <?php $vt = $p['verified_tier'] ?? 'none';
              echo $vt !== 'none' ? verified_badge($vt) : '<span style="color: var(--c-muted);">Not verified</span>'; ?>
    </span></div>
    <?php if (!empty($verification)): ?>
        <div class="info-row"><span class="k">Latest request</span><span class="v"><?= $verification['tier'] === 'selfie' ? 'Selfie + Identity' : 'Identity' ?> · <span class="pill <?= $verification['status'] === 'approved' ? 'green' : ($verification['status'] === 'rejected' ? 'red' : 'gold') ?>"><?= e(str_replace('_', ' ', $verification['status'])) ?></span></span></div>
        <div style="margin-top: .8rem;"><a href="/admin/verification/<?= (int)$verification['id'] ?>" class="btn btn-ghost btn-sm">Open verification request →</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($p): ?>
<div class="admin-card mb-3">
    <h3>Profile</h3>
    <div class="info-grid">
        <div>
            <?php foreach (['gender','dob','height_cm','marital_status','religion','community','caste','mother_tongue','diet'] as $k): ?>
                <div class="info-row"><span class="k"><?= e(str_replace('_',' ',$k)) ?></span><span class="v"><?= e($p[$k] ?: '—') ?></span></div>
            <?php endforeach; ?>
        </div>
        <div>
            <?php foreach (['country','state','city','education','profession','annual_income','family_type'] as $k): ?>
                <div class="info-row"><span class="k"><?= e(str_replace('_',' ',$k)) ?></span><span class="v"><?= e($p[$k] ?: '—') ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if ($p['about_me']): ?><p style="margin-top: 1rem;"><strong>About:</strong> <?= nl2br(e($p['about_me'])) ?></p><?php endif; ?>
</div>
<?php endif; ?>

<?php if ($sp): ?>
<div class="admin-card">
    <h3>Spiritual</h3>
    <?php foreach (['spiritual_path','guru','ishta_devata','daily_sadhana','favorite_scripture','mantra','fasting_practice','pilgrimage_done'] as $k): ?>
        <?php if (!empty($sp[$k])): ?>
            <div class="info-row"><span class="k"><?= e(str_replace('_',' ',$k)) ?></span><span class="v"><?= e($sp[$k]) ?></span></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
