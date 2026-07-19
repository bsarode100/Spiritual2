<?php /** @var array $rows */ ?>
<section class="section-tight">
<div class="container">
    <div class="flex-between mb-4" style="flex-wrap: wrap; gap: .5rem;">
        <div><span class="eyebrow">Premium insight</span><h1 style="margin:0;">Who Shortlisted You</h1></div>
        <div class="flex gap-1" style="flex-wrap: wrap;">
            <a href="/dashboard" class="btn btn-ghost btn-sm">Dashboard</a>
            <a href="/browse" class="btn btn-ghost btn-sm">Browse</a>
        </div>
    </div>

    <?php if (!$rows): ?>
        <div class="admin-card text-center" style="padding: 3rem;">
            <p style="color: var(--c-muted); margin: 0;">No one has shortlisted your profile yet.</p>
        </div>
    <?php else: ?>
        <div class="profiles-grid">
            <?php foreach ($rows as $m): $age = age_from_dob($m['dob']); $user = ['id' => $m['id'], 'name' => $m['name']]; ?>
                <article class="profile-card">
                    <div class="profile-photo">
                        <img src="<?= e(avatar_url($user)) ?>" alt="">
                    </div>
                    <div class="profile-body">
                        <h3><?= e($m['name']) ?><?php if ($age): ?>, <?= $age ?><?php endif; ?></h3>
                        <?= verified_badge($m['verified_tier'] ?? null, 'sm') ?>
                        <div class="profile-meta"><?= e($m['profession'] ?: 'Seeker') ?> - <?= e($m['city'] ?: '-') ?></div>
                        <p class="profile-about"><?= e($m['about_me'] ?? '') ?></p>
                        <p style="color: var(--c-muted); font-size: .86rem;">Shortlisted you on <?= e(date('M j, Y', strtotime($m['shortlisted_at']))) ?></p>
                        <a href="/member/<?= (int)$m['id'] ?>" class="btn btn-ghost btn-sm">View Profile</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</section>
