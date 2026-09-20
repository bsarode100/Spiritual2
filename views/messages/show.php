<?php /** @var array $other, $msgs, $interest, $threads */ $me = Auth::id(); ?>
<section class="section-tight chat-section">
<div class="container chat-container">
    <!-- Desktop-Only Breadcrumb Navigation -->
    <div class="flex-between mb-3 nav-desktop-link" style="flex-wrap: wrap; gap: .5rem;">
        <a href="/messages" class="btn btn-ghost btn-sm">← All Messages</a>
        <div class="flex gap-1" style="flex-wrap: wrap;">
            <a href="/member/<?= (int)$other['id'] ?>" class="btn btn-ghost btn-sm">👤 View Profile</a>
            <a href="/browse" class="btn btn-ghost btn-sm">🔍 Browse Seekers</a>
        </div>
    </div>

    <div class="msg-grid">
        <!-- Conversation Threads (Desktop Sidebar) -->
        <aside class="msg-threads">
            <div class="msg-threads-header">
                <h3>Conversations</h3>
            </div>
            <?php if (!$threads): ?>
                <div style="padding: 1.5rem; color: var(--c-muted); font-size: .9rem; text-align: center;">No other conversations yet.</div>
            <?php else: foreach ($threads as $t): $isActive = (int)$t['other_id'] === (int)$other['id']; ?>
                <a href="/messages/<?= (int)$t['other_id'] ?>" class="msg-thread <?= $isActive ? 'is-active' : '' ?>">
                    <div class="flex-between" style="gap: .6rem;">
                        <h4 style="margin:0;"><?= e($t['other_name']) ?></h4>
                        <?php if ((int)$t['unread'] > 0 && !$isActive): ?>
                            <span class="pill red" style="font-size: .7rem; padding: .15rem .55rem;"><?= (int)$t['unread'] ?></span>
                        <?php endif; ?>
                    </div>
                    <p><?= e($t['last_msg'] ? mb_substr($t['last_msg'], 0, 50) : 'Connected — say namaste 🙏') ?></p>
                </a>
            <?php endforeach; endif; ?>
        </aside>

        <!-- Active Chat Window -->
        <div class="msg-pane">
            <div class="msg-pane-head">
                <!-- Mobile Back Button -->
                <a href="/messages" class="chat-back-btn" aria-label="Back to all conversations">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                </a>

                <a href="/member/<?= (int)$other['id'] ?>" class="chat-avatar-link">
                    <img src="<?= e(avatar_url($other)) ?>" alt="" class="chat-avatar">
                </a>

                <div class="chat-head-info">
                    <h3 class="chat-name">
                        <a href="/member/<?= (int)$other['id'] ?>"><?= e($other['name']) ?></a>
                    </h3>
                    <div class="chat-status">
                        <?php $meta = array_filter([$other['profession'] ?? null, $other['city'] ?? null]); ?>
                        <?= $meta ? e(implode(' · ', $meta)) : 'Connected on ' . date('M j, Y', strtotime($interest['updated_at'] ?? 'now')) ?>
                    </div>
                </div>

                <a href="/member/<?= (int)$other['id'] ?>" class="btn btn-ghost btn-sm chat-profile-btn">
                    Profile →
                </a>
            </div>

            <div class="msg-pane-body" id="chatMessageBody">
                <?php if (!$msgs): ?>
                    <div class="chat-empty-state">
                        <div class="empty-icon">🪷</div>
                        <h4>Begin with reverence</h4>
                        <p>Say namaste, ask about their spiritual journey, or share what resonated with you.</p>
                    </div>
                <?php endif; ?>
                <?php
                $lastDate = null;
                foreach ($msgs as $m):
                    $mine = $m['sender_id'] == $me;
                    $date = date('Y-m-d', strtotime($m['created_at']));
                    if ($date !== $lastDate):
                        $lastDate = $date; ?>
                        <div class="chat-date-separator">
                            <span><?= date('l, M j, Y', strtotime($m['created_at'])) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="msg-bubble <?= $mine ? 'mine' : 'theirs' ?>">
                        <div class="bubble-content"><?= nl2br(e($m['body'])) ?></div>
                        <div class="msg-time">
                            <?= date('g:i a', strtotime($m['created_at'])) ?>
                            <?php if ($mine && !empty($m['read_at'])): ?> · <span title="Read">✓✓</span><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="post" action="/messages/<?= (int)$other['id'] ?>" class="msg-pane-form" id="chatForm">
                <?= csrf_field() ?>
                <input type="text" name="body" placeholder="Write a sacred message..." required autocomplete="off" maxlength="2000" class="chat-input" autofocus>
                <button type="submit" class="chat-send-btn" aria-label="Send message">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>
</section>
