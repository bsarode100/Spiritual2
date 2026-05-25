<?php /** @var array $received, $sent */ ?>
<section class="section-tight"><div class="container">
    <div class="flex-between mb-4">
        <div><span class="eyebrow">Connections</span><h1 style="margin:0;">Interests</h1></div>
        <a href="/dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <h2 style="font-size: 1.4rem;">Received <span style="color: var(--c-muted); font-size: 1rem;">(<?= count($received) ?>)</span></h2>
    <?php if (!$received): ?>
        <div class="admin-card mb-4"><p style="color: var(--c-muted); margin:0;">No interests received yet.</p></div>
    <?php else: ?>
        <div class="admin-card mb-4">
            <?php foreach ($received as $i): $age = age_from_dob($i['dob']); ?>
                <div class="flex-between" style="padding: 1rem 0; border-bottom: 1px solid var(--c-line);">
                    <div>
                        <h4 style="margin: 0 0 .25rem;"><a href="/member/<?= (int)$i['sender_id'] ?>"><?= e($i['name']) ?></a><?php if ($age): ?>, <?= $age ?><?php endif; ?></h4>
                        <div style="color: var(--c-muted); font-size: .9rem;">
                            <?= e($i['profession']) ?> · <?= e($i['city']) ?> · <?= date('M j, Y', strtotime($i['created_at'])) ?>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <?php if ($i['status'] === 'sent'): ?>
                            <form method="post" action="/interest/<?= (int)$i['id'] ?>/accept"><?= csrf_field() ?><button class="btn btn-primary btn-sm">Accept</button></form>
                            <form method="post" action="/interest/<?= (int)$i['id'] ?>/decline"><?= csrf_field() ?><button class="btn btn-ghost btn-sm">Decline</button></form>
                        <?php elseif ($i['status'] === 'accepted'): ?>
                            <a href="/messages/<?= (int)$i['sender_id'] ?>" class="btn btn-primary btn-sm">Message</a>
                        <?php else: ?>
                            <span class="pill <?= $i['status']==='declined'?'red':'gold' ?>"><?= e($i['status']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 style="font-size: 1.4rem;">Sent <span style="color: var(--c-muted); font-size: 1rem;">(<?= count($sent) ?>)</span></h2>
    <?php if (!$sent): ?>
        <div class="admin-card"><p style="color: var(--c-muted); margin:0;">Browse profiles and send interests to seekers who resonate with you.</p></div>
    <?php else: ?>
        <div class="admin-card">
            <?php foreach ($sent as $i): $age = age_from_dob($i['dob']); ?>
                <div class="flex-between" style="padding: 1rem 0; border-bottom: 1px solid var(--c-line);">
                    <div>
                        <h4 style="margin: 0 0 .25rem;"><a href="/member/<?= (int)$i['receiver_id'] ?>"><?= e($i['name']) ?></a><?php if ($age): ?>, <?= $age ?><?php endif; ?></h4>
                        <div style="color: var(--c-muted); font-size: .9rem;">
                            <?= e($i['profession']) ?> · <?= e($i['city']) ?> · sent <?= date('M j, Y', strtotime($i['created_at'])) ?>
                        </div>
                    </div>
                    <span class="pill <?= $i['status']==='accepted'?'green':($i['status']==='declined'?'red':'gold') ?>"><?= e($i['status']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div></section>
