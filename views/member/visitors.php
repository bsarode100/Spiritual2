<?php /** @var array $rows */ ?>
<section class="section-tight">
<div class="container">
    <div class="flex-between mb-4" style="flex-wrap: wrap; gap: .5rem;">
        <div><span class="eyebrow">Profile activity</span><h1 style="margin:0;">Profile Visitors</h1></div>
        <div class="flex gap-1" style="flex-wrap: wrap;">
            <a href="/dashboard" class="btn btn-ghost btn-sm">Dashboard</a>
            <a href="/packages" class="btn btn-gold btn-sm">Upgrade</a>
        </div>
    </div>

    <?php if (!$rows): ?>
        <div class="admin-card text-center" style="padding: 3rem;">
            <p style="color: var(--c-muted); margin: 0;">No profile visitors yet.</p>
        </div>
    <?php else: ?>
        <div class="admin-card">
            <?php foreach ($rows as $r): $age = age_from_dob($r['dob']); ?>
                <div class="flex-between" style="padding: 1rem 0; border-bottom: 1px solid var(--c-line); flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h4 style="margin: 0 0 .25rem;">
                            <a href="/member/<?= (int)$r['id'] ?>"><?= e($r['name']) ?></a><?php if ($age): ?>, <?= $age ?><?php endif; ?>
                            <?= verified_badge($r['verified_tier'] ?? null, 'sm') ?>
                        </h4>
                        <div style="color: var(--c-muted); font-size: .9rem;">
                            <?= e($r['profession'] ?: 'Seeker') ?> - <?= e($r['city'] ?: '-') ?> - viewed <?= e(date('M j, Y', strtotime($r['last_viewed_at']))) ?>
                            <?php if ((int)$r['view_count'] > 1): ?> (<?= (int)$r['view_count'] ?> times)<?php endif; ?>
                        </div>
                    </div>
                    <a href="/member/<?= (int)$r['id'] ?>" class="btn btn-primary btn-sm">View Profile</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</section>
