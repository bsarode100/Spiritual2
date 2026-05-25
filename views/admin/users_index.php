<?php /** @var array $rows; @var string $q */ ?>
<div class="admin-head">
    <div><span class="eyebrow">Community</span><h1>Members</h1></div>
    <form method="get" action="/admin/users" style="display: flex; gap: .5rem;">
        <input class="field" style="padding: .55rem 1rem; border: 1px solid var(--c-line); border-radius: var(--r-pill); min-width: 280px;" type="text" name="q" value="<?= e($q) ?>" placeholder="Search name or email...">
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
</div>

<div class="admin-card">
    <table class="tbl">
        <thead><tr><th>Name</th><th>Email</th><th>Gender</th><th>City</th><th>Status</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><a href="/admin/users/<?= (int)$r['id'] ?>"><?= e($r['name']) ?></a></td>
                <td><?= e($r['email']) ?></td>
                <td><?= e($r['gender'] ?? '—') ?></td>
                <td><?= e($r['city'] ?? '—') ?></td>
                <td><span class="pill <?= $r['status']==='active'?'green':($r['status']==='blocked'?'red':'gold') ?>"><?= e($r['status']) ?></span></td>
                <td><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
                <td class="actions">
                    <form method="post" action="/admin/users/<?= (int)$r['id'] ?>/toggle"><?= csrf_field() ?><button class="btn btn-ghost btn-sm"><?= $r['status']==='blocked' ? 'Unblock' : 'Block' ?></button></form>
                    <form method="post" action="/admin/users/<?= (int)$r['id'] ?>/delete" onsubmit="return confirm('Delete this member?')"><?= csrf_field() ?><button class="btn btn-ghost btn-sm" style="color: var(--c-maroon); border-color: var(--c-maroon);">Delete</button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
