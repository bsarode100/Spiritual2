<?php /** @var array $rows */
$protected = ['about','privacy','terms','contact'];
?>
<div class="admin-head">
    <h1>CMS Pages</h1>
    <a href="/admin/pages/new" class="btn btn-primary btn-sm">＋ New Page</a>
</div>
<div class="admin-card">
    <p style="color: var(--c-muted);">Edit static pages — About, Privacy, Terms, Refund, FAQ, Disclaimer, etc. Body supports full HTML. Use <strong>＋ New Page</strong> to add any custom page; it will be live at <code>/page/your-slug</code>.</p>
    <table class="tbl">
        <thead><tr><th>Slug</th><th>Title</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r):
            $isProtected = in_array($r['slug'], $protected, true);
            // Built-in pages have a short URL (e.g. /privacy); custom pages live at /page/{slug}
            $publicUrl = $isProtected ? ('/' . $r['slug']) : ('/page/' . $r['slug']);
        ?>
            <tr>
                <td>
                    <code><?= e($publicUrl) ?></code>
                    <?php if ($isProtected): ?>
                        <span class="pill" style="background: #eef; color: #336; margin-left: .25rem;">built-in</span>
                    <?php endif; ?>
                </td>
                <td><?= e($r['title']) ?></td>
                <td><span class="pill <?= $r['published'] ? 'green' : 'red' ?>"><?= $r['published'] ? 'Live' : 'Hidden' ?></span></td>
                <td class="actions">
                    <a href="<?= e($publicUrl) ?>" target="_blank" class="btn btn-ghost btn-sm">View</a>
                    <a href="/admin/pages/<?= (int)$r['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
