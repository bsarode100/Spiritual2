<?php /** @var array $rows */ ?>
<div class="admin-head">
    <div><span class="eyebrow">Pricing</span><h1>Packages</h1></div>
    <a href="/admin/packages/new" class="btn btn-primary">+ New Package</a>
</div>
<div class="admin-card">
    <table class="tbl">
        <thead><tr><th>Name</th><th>Price</th><th>Duration</th><th>Highlighted</th><th>Active</th><th>Order</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><strong><?= e($r['name']) ?></strong><br><span style="color: var(--c-muted); font-size: .82rem;"><?= e($r['tagline']) ?></span></td>
                <td><?= $r['price'] > 0 ? '₹' . number_format((float)$r['price']) : 'Free' ?></td>
                <td><?= (int)$r['duration_days'] ?> days</td>
                <td><?= $r['highlighted'] ? '⭐ Yes' : '—' ?></td>
                <td><span class="pill <?= $r['is_active'] ? 'green' : 'red' ?>"><?= $r['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                <td><?= (int)$r['display_order'] ?></td>
                <td class="actions">
                    <a href="/admin/packages/<?= (int)$r['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                    <form method="post" action="/admin/packages/<?= (int)$r['id'] ?>/delete" onsubmit="return confirm('Delete this package?')"><?= csrf_field() ?><button class="btn btn-ghost btn-sm" style="color: var(--c-maroon);">Del</button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
