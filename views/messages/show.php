<?php /** @var array $other, $msgs, $interest, $threads */ $me = Auth::id(); ?>
<section class="section-tight"><div class="container">
    <div class="flex-between mb-3" style="flex-wrap: wrap; gap: .5rem;">
        <a href="/messages" class="btn btn-ghost btn-sm">← All Messages</a>
        <div class="flex gap-1" style="flex-wrap: wrap;">
            <a href="/browse" class="btn btn-ghost btn-sm">🔍 Browse Profiles</a>
            <a href="/member/<?= (int)$other['id'] ?>" class="btn btn-ghost btn-sm">View Profile</a>
            <a href="/dashboard" class="btn btn-ghost btn-sm">🏠 Dashboard</a>
        </div>
    </div>

    <div class="msg-grid">
        <aside class="msg-threads">
            <?php if (!$threads): ?>
                <div style="padding: 1.2rem; color: var(--c-muted); font-size: .9rem;">No other conversations yet.</div>
            <?php else: foreach ($threads as $t): $isActive = (int)$t['other_id'] === (int)$other['id']; ?>
                <a href="/messages/<?= (int)$t['other_id'] ?>" class="msg-thread <?= $isActive ? 'is-active' : '' ?>">
                    <div class="flex-between" style="gap: .6rem;">
                        <h4 style="margin:0;"><?= e($t['other_name']) ?></h4>
                        <?php if ((int)$t['unread'] > 0 && !$isActive): ?>
                            <span class="pill red" style="font-size: .7rem; padding: .15rem .55rem;"><?= (int)$t['unread'] ?></span>
                        <?php endif; ?>
                    </div>
                    <p><?= e($t['last_msg'] ? mb_substr($t['last_msg'], 0, 60) : 'Connected — say namaste 🙏') ?></p>
                </a>
            <?php endforeach; endif; ?>
        </aside>

        <div class="msg-pane">
            <div class="msg-pane-head">
                <img src="<?= e(avatar_url($other)) ?>" alt="" style="width: 44px; height: 44px; border-radius: 50%;">
                <div style="flex:1;">
                    <h3 style="margin: 0; font-size: 1.1rem;"><a href="/member/<?= (int)$other['id'] ?>" style="color: var(--c-ink);"><?= e($other['name']) ?></a></h3>
                    <div style="color: var(--c-muted); font-size: .82rem;">
                        <?php $meta = array_filter([$other['profession'] ?? null, $other['city'] ?? null]); ?>
                        <?= $meta ? e(implode(' · ', $meta)) : 'Connected on ' . date('M j, Y', strtotime($interest['updated_at'])) ?>
                    </div>
                </div>
            </div>
            <div class="msg-pane-body">
                <?php if (!$msgs): ?>
                    <p style="text-align: center; color: var(--c-muted); font-style: italic; padding: 3rem 0;">No messages yet. Say namaste 🙏</p>
                <?php endif; ?>
                <?php
                $lastDate = null;
                foreach ($msgs as $m):
                    $mine = $m['sender_id'] == $me;
                    $date = date('Y-m-d', strtotime($m['created_at']));
                    if ($date !== $lastDate):
                        $lastDate = $date; ?>
                        <div style="text-align:center; color: var(--c-muted); font-size: .78rem; margin: 1rem 0 .6rem;">
                            <?= date('l, M j, Y', strtotime($m['created_at'])) ?>
                        </div>
                    <?php endif; ?>
                    <div class="msg-bubble <?= $mine ? 'mine' : 'theirs' ?>">
                        <?= nl2br(e($m['body'])) ?>
                        <div class="msg-time">
                            <?= date('g:i a', strtotime($m['created_at'])) ?>
                            <?php if ($mine && !empty($m['read_at'])): ?> · seen<?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="post" action="/messages/<?= (int)$other['id'] ?>" class="msg-pane-form">
                <?= csrf_field() ?>
                <input type="text" name="body" placeholder="Type a message..." required autocomplete="off" maxlength="2000">
                <button class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>

    <div class="admin-card text-center mt-4" style="padding: 1.4rem;">
        <p style="color: var(--c-muted); margin: 0 0 .8rem;">Want to meet more seekers on the same path?</p>
        <div class="flex gap-1" style="justify-content: center; flex-wrap: wrap;">
            <a href="/browse" class="btn btn-primary btn-sm">🔍 Browse New Profiles</a>
            <a href="/interests" class="btn btn-ghost btn-sm">💌 My Interests</a>
            <a href="/dashboard" class="btn btn-ghost btn-sm">🏠 Dashboard</a>
        </div>
    </div>
</div></section>
