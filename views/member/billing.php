<?php
/** @var array $payments, $subs, $membership */
$me = Auth::user();
$plan = $membership['plan'];
$statusPill = fn(string $s) => match ($s) {
    'active', 'paid'        => 'green',
    'pending', 'created'    => 'gold',
    'expired', 'cancelled'  => 'red',
    'failed', 'refunded'    => 'red',
    default                 => 'gold',
};
?>
<section class="section-tight">
<div class="container">
    <div class="dash-grid">
        <aside class="dash-side">
            <div class="me">
                <img src="<?= e(avatar_url($me)) ?>" alt="">
                <div>
                    <h4><?= e($me['name']) ?></h4>
                    <span><?= e($plan['name']) ?></span>
                </div>
            </div>
            <ul class="dash-nav" style="list-style: none;">
                <li><a href="/dashboard">Dashboard</a></li>
                <li><a href="/profile/edit">Edit Profile</a></li>
                <li><a href="/profile/photos">Photos</a></li>
                <li><a href="/browse">Browse</a></li>
                <li><a href="/interests">Interests</a></li>
                <li><a href="/shortlist">Shortlist</a></li>
                <li><a href="/visitors">Visitors</a></li>
                <li><a href="/shortlisted-by">Shortlisted Me</a></li>
                <li><a href="/packages">Membership</a></li>
                <li><a href="/billing" class="is-active">Billing</a></li>
                <li><a href="/messages">Messages</a></li>
                <li><a href="/settings">Settings</a></li>
                <li><a href="/logout">Sign out</a></li>
            </ul>
        </aside>

        <div>
            <div class="flex-between mb-3">
                <div>
                    <span class="eyebrow">Membership</span>
                    <h1 style="margin: 0;">Billing &amp; Receipts</h1>
                </div>
                <a href="/packages" class="btn btn-gold btn-sm">Renew or Upgrade</a>
            </div>

            <div class="membership-card mb-4">
                <div class="membership-main">
                    <span class="eyebrow">Current Plan</span>
                    <h2><?= e($plan['name']) ?></h2>
                    <p><?= e($plan['tagline'] ?? '') ?></p>
                    <div class="membership-actions">
                        <a href="/packages" class="btn btn-primary btn-sm">Change Plan</a>
                        <a href="/addons" class="btn btn-ghost btn-sm">Buy Add-on</a>
                    </div>
                </div>
                <div class="membership-metrics">
                    <div><span>Expiry</span><strong><?= $membership['expires_at'] ? e(date('M j, Y', strtotime($membership['expires_at']))) : 'Lifetime' ?></strong></div>
                    <div><span>Days Left</span><strong><?= $membership['days_left'] === null ? '—' : (int)$membership['days_left'] ?></strong></div>
                    <div><span>Priority</span><strong><?= e($membership['priority_label']) ?></strong></div>
                    <div><span>Badge</span><strong><?= $membership['badge'] ? e($membership['badge']) : 'None' ?></strong></div>
                </div>
            </div>

            <div class="admin-card mb-4">
                <h3 style="margin-bottom: 1rem;">Membership History</h3>
                <?php if (!$subs): ?>
                    <p style="color: var(--c-muted);">You have no membership records yet. <a href="/packages">Choose a plan →</a></p>
                <?php else: ?>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Purchased</th>
                            <th>Expiry</th>
                            <th>Amount</th>
                            <th>Payment Ref</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subs as $s): ?>
                        <tr>
                            <td><strong><?= e($s['package_name']) ?></strong><br><span style="color: var(--c-muted); font-size: .82rem;"><?= e($s['package_slug']) ?></span></td>
                            <td><span class="pill <?= $statusPill($s['status']) ?>"><?= e($s['status']) ?></span></td>
                            <td><?= e(date('M j, Y', strtotime($s['purchased_at'] ?? $s['starts_at']))) ?></td>
                            <td><?= e(date('M j, Y', strtotime($s['ends_at']))) ?></td>
                            <td>Rs <?= number_format((float)$s['amount'], 0) ?></td>
                            <td><code style="font-size: .78rem;"><?= e($s['payment_ref'] ?: '—') ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <div class="admin-card">
                <h3 style="margin-bottom: 1rem;">All Payments</h3>
                <?php if (!$payments): ?>
                    <p style="color: var(--c-muted);">No payment records yet.</p>
                <?php else: ?>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Kind</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Gateway ID</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td><?= e(date('M j, Y', strtotime($p['created_at']))) ?></td>
                            <td><?= e($p['item_name']) ?></td>
                            <td><span class="pill"><?= e(ucfirst($p['purchase_type'] ?? 'package')) ?></span></td>
                            <td>Rs <?= number_format((float)$p['amount'], 0) ?></td>
                            <td><span class="pill <?= $statusPill($p['status']) ?>"><?= e($p['status']) ?></span></td>
                            <td>
                                <?php if (!empty($p['gateway_payment_id'])): ?>
                                    <code style="font-size: .78rem;"><?= e($p['gateway_payment_id']) ?></code>
                                <?php elseif (!empty($p['gateway_order_id'])): ?>
                                    <code style="font-size: .78rem;"><?= e($p['gateway_order_id']) ?></code>
                                <?php else: ?>
                                    <span style="color: var(--c-muted);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</section>
