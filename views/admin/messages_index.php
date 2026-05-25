<?php /** @var array $rows */ ?>
<div class="admin-head"><h1>Contact Messages</h1></div>
<?php if (!$rows): ?>
    <div class="admin-card text-center" style="padding: 3rem;"><p>No messages yet.</p></div>
<?php else: ?>
<?php foreach ($rows as $m): ?>
    <div class="admin-card mb-3">
        <div class="flex-between mb-2">
            <div>
                <h3 style="margin: 0;"><?= e($m['name']) ?></h3>
                <div style="color: var(--c-muted); font-size: .9rem;">
                    <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a>
                    <?php if ($m['phone']): ?> · <?= e($m['phone']) ?><?php endif; ?>
                    · <?= date('M j, Y g:i a', strtotime($m['created_at'])) ?>
                </div>
            </div>
            <form method="post" action="/admin/messages/<?= (int)$m['id'] ?>/delete" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button class="btn btn-ghost btn-sm" style="color: var(--c-maroon);">Delete</button></form>
        </div>
        <?php if ($m['subject']): ?><p style="font-weight: 600;"><?= e($m['subject']) ?></p><?php endif; ?>
        <p><?= nl2br(e($m['message'])) ?></p>
    </div>
<?php endforeach; ?>
<?php endif; ?>
