<?php /** @var array $rows */ ?>
<div class="admin-head"><h1>CMS Pages</h1></div>
<div class="admin-card">
    <p style="color: var(--c-muted);">Edit static pages — About, Privacy, Terms, Contact intro, etc. Body supports full HTML.</p>
    <table class="tbl">
        <thead><tr><th>Slug</th><th>Title</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><code>/page/<?= e($r['slug']) ?></code></td>
                <td><?= e($r['title']) ?></td>
                <td><span class="pill <?= $r['published'] ? 'green' : 'red' ?>"><?= $r['published'] ? 'Live' : 'Hidden' ?></span></td>
                <td class="actions">
                    <a href="/page/<?= e($r['slug']) ?>" target="_blank" class="btn btn-ghost btn-sm">View</a>
                    <a href="/admin/pages/<?= (int)$r['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
