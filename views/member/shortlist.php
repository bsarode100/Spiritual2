<?php /** @var array $rows */ ?>
<section class="section-tight"><div class="container">
    <div class="flex-between mb-4">
        <div><span class="eyebrow">Saved for later</span><h1 style="margin:0;">Shortlist</h1></div>
        <a href="/dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>
    <?php if (!$rows): ?>
        <div class="admin-card text-center" style="padding: 3rem;">
            <p style="color: var(--c-muted); margin: 0;">No shortlisted profiles yet. ⭐ Shortlist any profile you'd like to revisit.</p>
        </div>
    <?php else: ?>
        <div class="profiles-grid">
            <?php foreach ($rows as $m): $age = age_from_dob($m['dob']); $user = ['id' => $m['id'], 'name' => $m['name']]; ?>
                <article class="profile-card">
                    <div class="profile-photo">
                        <img src="<?= e(avatar_url($user)) ?>" alt="">
                        <?php if ($m['spiritual_path']): ?><span class="profile-badge"><?= e($m['spiritual_path']) ?></span><?php endif; ?>
                    </div>
                    <div class="profile-body">
                        <h3><?= e($m['name']) ?><?php if ($age): ?>, <?= $age ?><?php endif; ?></h3>
                        <div class="profile-meta"><?= e($m['profession'] ?: 'Seeker') ?> · <?= e($m['city']) ?></div>
                        <p class="profile-about"><?= e($m['about_me'] ?? '') ?></p>
                        <a href="/member/<?= (int)$m['id'] ?>" class="btn btn-ghost btn-sm">View Profile</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div></section>
