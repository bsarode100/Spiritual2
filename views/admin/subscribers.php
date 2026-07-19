<?php /** @var array $rows, $packages, $members, $stats */ ?>
<div class="admin-head">
    <div><span class="eyebrow">Revenue</span><h1>Subscribers</h1></div>
    <a href="/admin/packages" class="btn btn-ghost btn-sm">Edit Plans</a>
</div>

<div class="stat-cards mb-4">
    <div class="stat-card"><div class="label">Active Subscribers</div><div class="value"><?= number_format((int)$stats['active']) ?></div></div>
    <div class="stat-card"><div class="label">Expired</div><div class="value"><?= number_format((int)$stats['expired']) ?></div></div>
    <div class="stat-card"><div class="label">Paid Orders</div><div class="value"><?= number_format((int)$stats['paid_payments']) ?></div></div>
    <div class="stat-card"><div class="label">Revenue</div><div class="value">Rs <?= number_format((float)$stats['revenue'], 0) ?></div></div>
</div>

<div class="admin-card mb-4">
    <h3>Grant Manual Membership</h3>
    <form method="post" action="/admin/subscribers/grant" class="form-grid-3">
        <?= csrf_field() ?>
        <div class="field">
            <label>Member</label>
            <select name="user_id" required>
                <option value="">Choose member</option>
                <?php foreach ($members as $m): ?>
                    <option value="<?= (int)$m['id'] ?>"><?= e($m['name']) ?> - <?= e($m['email']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label>Plan</label>
            <select name="package_id" required>
                <option value="">Choose plan</option>
                <?php foreach ($packages as $p): ?>
                    <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?> - Rs <?= number_format((float)$p['price'], 0) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label>Custom Days</label>
            <input type="number" name="days" min="0" placeholder="Use plan duration">
        </div>
        <div class="full">
            <button class="btn btn-primary">Grant Membership</button>
        </div>
    </form>
</div>

<div class="admin-card">
    <h3>Membership Records</h3>
    <table class="tbl">
        <thead>
            <tr>
                <th>Member</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Purchased</th>
                <th>Expiry</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><strong><?= e($r['user_name']) ?></strong><br><span style="color: var(--c-muted); font-size: .82rem;"><?= e($r['user_email']) ?></span></td>
                <td><?= e($r['package_name']) ?><br><span style="color: var(--c-muted); font-size: .82rem;"><?= e($r['package_slug']) ?></span></td>
                <td><span class="pill <?= $r['status'] === 'active' ? 'green' : ($r['status'] === 'cancelled' ? 'red' : 'gold') ?>"><?= e($r['status']) ?></span></td>
                <td><?= !empty($r['purchased_at']) ? e(date('M j, Y', strtotime($r['purchased_at']))) : e(date('M j, Y', strtotime($r['starts_at']))) ?></td>
                <td><?= e(date('M j, Y', strtotime($r['ends_at']))) ?></td>
                <td>Rs <?= number_format((float)$r['amount'], 0) ?></td>
                <td>
                    <?php if (!empty($r['gateway_payment_id'])): ?>
                        <code><?= e($r['gateway_payment_id']) ?></code>
                    <?php else: ?>
                        <span style="color: var(--c-muted);">Manual</span>
                    <?php endif; ?>
                </td>
                <td class="actions" style="min-width: 220px;">
                    <form method="post" action="/admin/subscribers/<?= (int)$r['id'] ?>/extend" style="display:flex; gap:.35rem; align-items:center;">
                        <?= csrf_field() ?>
                        <input type="number" name="days" value="30" min="1" style="width:72px; padding:.45rem; border:1px solid var(--c-line); border-radius:6px;">
                        <button class="btn btn-ghost btn-sm">Extend</button>
                    </form>
                    <?php if ($r['status'] === 'active'): ?>
                        <form method="post" action="/admin/subscribers/<?= (int)$r['id'] ?>/cancel" onsubmit="return confirm('Cancel this membership?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-ghost btn-sm" style="color: var(--c-maroon);">Cancel</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
