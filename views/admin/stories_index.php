<?php /** @var array $rows */ ?>
<div class="admin-head">
    <div><span class="eyebrow">Testimonials</span><h1>Happy Stories</h1></div>
    <a href="/admin/stories/new" class="btn btn-primary">+ New Story</a>
</div>
<div class="admin-card">
    <table class="tbl">
        <thead><tr><th>Couple</th><th>Married On</th><th>Featured</th><th>Snippet</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><strong><?= e($r['couple_name']) ?></strong></td>
                <td><?= $r['married_on'] ? date('M Y', strtotime($r['married_on'])) : '—' ?></td>
                <td><?= $r['is_featured'] ? '⭐' : '—' ?></td>
                <td style="color: var(--c-muted); max-width: 360px;"><?= e(mb_substr($r['story'], 0, 100)) ?>…</td>
                <td class="actions">
                    <a href="/admin/stories/<?= (int)$r['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                    <form method="post" action="/admin/stories/<?= (int)$r['id'] ?>/delete" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button class="btn btn-ghost btn-sm" style="color: var(--c-maroon);">Del</button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
