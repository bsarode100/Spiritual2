<?php /** @var array $totals, $byPlan, $byAddon, $monthly */ ?>
<div class="admin-head">
    <div><span class="eyebrow">Finance</span><h1>Revenue</h1></div>
    <a href="/admin/subscribers" class="btn btn-ghost btn-sm">Manage Subscribers</a>
</div>

<div class="stat-cards mb-4">
    <div class="stat-card"><div class="label">Total Revenue</div><div class="value">Rs <?= number_format((float)$totals['all'], 0) ?></div></div>
    <div class="stat-card"><div class="label">This Month</div><div class="value" style="color: #2E7D32;">Rs <?= number_format((float)$totals['this_month'], 0) ?></div></div>
    <div class="stat-card"><div class="label">Plan Revenue</div><div class="value">Rs <?= number_format((float)$totals['package'], 0) ?></div></div>
    <div class="stat-card"><div class="label">Add-on Revenue</div><div class="value">Rs <?= number_format((float)$totals['addon'], 0) ?></div></div>
</div>
<div class="stat-cards mb-4">
    <div class="stat-card"><div class="label">Verification Revenue</div><div class="value">Rs <?= number_format((float)$totals['verification'], 0) ?></div></div>
</div>

<div class="admin-card mb-4">
    <h3 style="margin-bottom: 1rem;">Revenue by Plan</h3>
    <table class="tbl">
        <thead>
            <tr>
                <th>Plan</th>
                <th>Price</th>
                <th>Active Subscribers</th>
                <th>Paid Orders</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($byPlan as $p): ?>
            <tr>
                <td><strong><?= e($p['name']) ?></strong><br><span style="color: var(--c-muted); font-size: .82rem;"><?= e($p['slug']) ?></span></td>
                <td>Rs <?= number_format((float)$p['price'], 0) ?></td>
                <td><?= number_format((int)$p['active_subs']) ?></td>
                <td><?= number_format((int)$p['paid_orders']) ?></td>
                <td>Rs <?= number_format((float)$p['revenue'], 0) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="admin-card mb-4">
    <h3 style="margin-bottom: 1rem;">Revenue by Add-on</h3>
    <?php if (!$byAddon): ?>
        <p style="color: var(--c-muted);">No add-ons configured.</p>
    <?php else: ?>
    <table class="tbl">
        <thead>
            <tr><th>Add-on</th><th>Paid Orders</th><th>Total Revenue</th></tr>
        </thead>
        <tbody>
        <?php foreach ($byAddon as $a): ?>
            <tr>
                <td><strong><?= e($a['name']) ?></strong><br><span style="color: var(--c-muted); font-size: .82rem;"><?= e($a['slug']) ?></span></td>
                <td><?= number_format((int)$a['paid_orders']) ?></td>
                <td>Rs <?= number_format((float)$a['revenue'], 0) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="admin-card">
    <h3 style="margin-bottom: 1rem;">Monthly Revenue (last 12 months)</h3>
    <?php if (!$monthly): ?>
        <p style="color: var(--c-muted);">No paid orders yet.</p>
    <?php else: ?>
    <table class="tbl">
        <thead>
            <tr><th>Month</th><th>Orders</th><th>Revenue</th></tr>
        </thead>
        <tbody>
        <?php foreach ($monthly as $m): ?>
            <tr>
                <td><?= e(date('F Y', strtotime($m['ym'] . '-01'))) ?></td>
                <td><?= number_format((int)$m['orders']) ?></td>
                <td>Rs <?= number_format((float)$m['revenue'], 0) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
