<?php /** @var array $threads */ ?>
<section class="section-tight"><div class="container">
    <div class="flex-between mb-4">
        <div><span class="eyebrow">Conversations</span><h1 style="margin:0;">Messages</h1></div>
        <a href="/dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <?php if (!$threads): ?>
        <div class="admin-card text-center" style="padding: 4rem 2rem;">
            <p style="font-family: var(--f-display); font-size: 1.4rem;">No conversations yet.</p>
            <p style="color: var(--c-muted);">Once an interest is accepted, you can begin a conversation here.</p>
            <a href="/browse" class="btn btn-primary mt-2">Browse Profiles →</a>
        </div>
    <?php else: ?>
        <div class="admin-card" style="padding: 0;">
            <?php foreach ($threads as $t): ?>
                <a href="/messages/<?= (int)$t['other_id'] ?>" class="msg-thread" style="border-bottom: 1px solid var(--c-line);">
                    <h4><?= e($t['other_name']) ?></h4>
                    <p><?= e(mb_substr($t['last_msg'] ?? '', 0, 80)) ?></p>
                    <div style="color: var(--c-muted); font-size: .76rem; margin-top: .3rem;"><?= date('M j · g:i a', strtotime($t['last_at'])) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div></section>
