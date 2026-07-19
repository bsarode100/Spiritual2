<?php /** @var array $stats, $recent */ ?>
<div class="admin-head">
    <div>
        <span class="eyebrow">Overview</span>
        <h1>Dashboard</h1>
    </div>
    <div style="display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;">
        <form method="post" action="/admin/test-mail" style="margin: 0;">
            <?= csrf_field() ?>
            <button class="btn btn-ghost btn-sm">Send test email</button>
        </form>
        <span style="color: var(--c-muted);"><?= date('l, F j, Y') ?></span>
    </div>
</div>

<div class="stat-cards mb-4">
    <div class="stat-card"><div class="label">Members</div><div class="value"><?= number_format((int)$stats['members']) ?></div></div>
    <div class="stat-card"><div class="label">Active</div><div class="value" style="color: #2E7D32;"><?= number_format((int)$stats['active']) ?></div></div>
    <div class="stat-card"><div class="label">Blocked</div><div class="value" style="color: var(--c-maroon);"><?= number_format((int)$stats['blocked']) ?></div></div>
    <div class="stat-card"><div class="label">Interests</div><div class="value"><?= number_format((int)$stats['interests']) ?></div></div>
</div>
<div class="stat-cards mb-4">
    <div class="stat-card"><div class="label">Blog Posts</div><div class="value"><?= (int)$stats['posts'] ?></div></div>
    <div class="stat-card"><div class="label">Happy Stories</div><div class="value"><?= (int)$stats['stories'] ?></div></div>
    <div class="stat-card"><div class="label">Packages</div><div class="value"><?= (int)$stats['packages'] ?></div></div>
    <div class="stat-card"><div class="label">Unread Mail</div><div class="value" style="color: var(--c-saffron);"><?= (int)$stats['messages'] ?></div></div>
</div>
<div class="stat-cards mb-4">
    <div class="stat-card"><div class="label">Active Subscribers</div><div class="value"><?= number_format((int)$stats['subscribers']) ?></div></div>
    <div class="stat-card"><div class="label">Plan Revenue</div><div class="value">Rs <?= number_format((float)$stats['revenue'], 0) ?></div></div>
    <div class="stat-card"><div class="label">Revenue Report</div><div class="value" style="font-size: 1rem;"><a href="/admin/revenue">View</a></div></div>
    <div class="stat-card"><div class="label">Pricing Matrix</div><div class="value" style="font-size: 1rem;"><a href="/admin/packages">Edit</a></div></div>
</div>

<div class="admin-card">
    <h3 style="margin-bottom: 1.2rem;">Recent registrations</h3>
    <table class="tbl">
        <thead><tr><th>Name</th><th>Email</th><th>Gender</th><th>City</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($recent as $r): ?>
            <tr>
                <td><?= e($r['name']) ?></td>
                <td><?= e($r['email']) ?></td>
                <td><?= e($r['gender'] ?? '—') ?></td>
                <td><?= e($r['city'] ?? '—') ?></td>
                <td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td>
                <td><a href="/admin/users/<?= (int)$r['id'] ?>" class="btn btn-ghost btn-sm">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
