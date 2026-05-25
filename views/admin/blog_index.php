<?php /** @var array $rows */ ?>
<div class="admin-head">
    <div><span class="eyebrow">Content</span><h1>Blog Posts</h1></div>
    <a href="/admin/blog/new" class="btn btn-primary">+ New Post</a>
</div>
<div class="admin-card">
    <table class="tbl">
        <thead><tr><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Published</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><a href="/admin/blog/<?= (int)$r['id'] ?>/edit"><?= e($r['title']) ?></a></td>
                <td><?= e($r['category']) ?></td>
                <td><?= e($r['author_name']) ?></td>
                <td><span class="pill <?= $r['published'] ? 'green' : 'gold' ?>"><?= $r['published'] ? 'Published' : 'Draft' ?></span></td>
                <td><?= $r['published_at'] ? date('M j, Y', strtotime($r['published_at'])) : '—' ?></td>
                <td class="actions">
                    <a href="/blog/<?= e($r['slug']) ?>" target="_blank" class="btn btn-ghost btn-sm">View</a>
                    <a href="/admin/blog/<?= (int)$r['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                    <form method="post" action="/admin/blog/<?= (int)$r['id'] ?>/delete" onsubmit="return confirm('Delete this post?')"><?= csrf_field() ?><button class="btn btn-ghost btn-sm" style="color: var(--c-maroon);">Del</button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
