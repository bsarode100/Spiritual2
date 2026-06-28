<?php /** @var array $other, $msgs, $interest */ $me = Auth::id(); ?>
<section class="section-tight"><div class="container">
    <a href="/messages" class="btn btn-ghost btn-sm mb-3">← All Messages</a>

    <div class="msg-grid">
        <div class="msg-pane" style="grid-column: 1 / -1;">
            <div class="msg-pane-head">
                <img src="<?= e(avatar_url($other)) ?>" alt="" style="width: 44px; height: 44px; border-radius: 50%;">
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem;"><a href="/member/<?= (int)$other['id'] ?>" style="color: var(--c-ink);"><?= e($other['name']) ?></a></h3>
                    <div style="color: var(--c-muted); font-size: .82rem;">Active member</div>
                </div>
            </div>
            <div class="msg-pane-body">
                <?php if (!$msgs): ?>
                    <p style="text-align: center; color: var(--c-muted); font-style: italic; padding: 3rem 0;">No messages yet. Say namaste 🙏</p>
                <?php endif; ?>
                <?php foreach ($msgs as $m): $mine = $m['sender_id'] == $me; ?>
                    <div class="msg-bubble <?= $mine ? 'mine' : 'theirs' ?>">
                        <?= nl2br(e($m['body'])) ?>
                        <div class="msg-time"><?= date('M j · g:i a', strtotime($m['created_at'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="post" action="/messages/<?= (int)$other['id'] ?>" class="msg-pane-form">
                <?= csrf_field() ?>
                <input type="text" name="body" placeholder="Type a message..." required autocomplete="off">
                <button class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>
</div></section>
