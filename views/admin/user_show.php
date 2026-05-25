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
</div>

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
