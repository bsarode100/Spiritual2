<?php /** @var array $rows */ ?>
<div class="admin-head">
    <div><span class="eyebrow">Pricing</span><h1>Membership Plans</h1></div>
    <a href="/admin/packages/new" class="btn btn-primary">+ New Plan</a>
</div>

<div class="admin-card">
    <table class="tbl">
        <thead>
            <tr>
                <th>Plan</th>
                <th>Price</th>
                <th>Duration</th>
                <th>Contacts</th>
                <th>Priority</th>
                <th>Boosts</th>
                <th>Featured</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td>
                    <strong><?= e($r['name']) ?></strong>
                    <br><span style="color: var(--c-muted); font-size: .82rem;"><?= e($r['slug'] ?: 'custom') ?> · <?= e($r['tagline']) ?></span>
                    <?php if (!empty($r['ribbon'])): ?><br><span class="pill gold"><?= e($r['ribbon']) ?></span><?php endif; ?>
                </td>
                <td><?= (float)$r['price'] > 0 ? 'Rs ' . number_format((float)$r['price']) : 'Free' ?></td>
                <td><?= (int)$r['duration_months'] === 0 ? 'Lifetime' : (int)$r['duration_months'] . ' months' ?></td>
                <td><?= (float)$r['price'] <= 0 ? '0' : ((int)$r['contacts_limit'] === 0 ? 'Unlimited' : (int)$r['contacts_limit']) ?></td>
                <td><?= e(plan_priority_label($r)) ?></td>
                <td><?= (int)$r['boosts_per_month'] ?>/mo</td>
                <td>
                    <?php if ((int)$r['always_featured'] === 1): ?>
                        Always
                    <?php elseif ((int)$r['featured_days'] > 0): ?>
                        <?= (int)$r['featured_days'] ?> days
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><span class="pill <?= $r['is_active'] ? 'green' : 'red' ?>"><?= $r['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                <td class="actions">
                    <a href="/admin/packages/<?= (int)$r['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                    <form method="post" action="/admin/packages/<?= (int)$r['id'] ?>/delete" onsubmit="return confirm('Delete or hide this plan?')">
                        <?= csrf_field() ?>
                        <button class="btn btn-ghost btn-sm" style="color: var(--c-maroon);">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
