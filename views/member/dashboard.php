<?php
/** @var array $stats, $matches, $recent_interests, $membership */
$me = Auth::user();
$plan = $membership['plan'];
$contactsText = $membership['contacts_left'] === null ? 'Unlimited' : (string)(int)$membership['contacts_left'];
$interestsText = $membership['interests_left'] === null ? 'Unlimited' : (string)(int)$membership['interests_left'];
$shortlistsText = $membership['shortlists_left'] === null ? 'Unlimited' : (string)(int)$membership['shortlists_left'];
?>
<section class="section-tight">
<div class="container">
    <div class="dash-grid">
        <aside class="dash-side">
            <div class="me">
                <img src="<?= e(avatar_url($me)) ?>" alt="">
                <div>
                    <h4><?= e($me['name']) ?></h4>
                    <span><?= e($plan['name']) ?></span>
                </div>
            </div>
            <ul class="dash-nav" style="list-style: none;">
                <li><a href="/dashboard" class="is-active">Dashboard</a></li>
                <li><a href="/profile/edit">Edit Profile</a></li>
                <li><a href="/profile/photos">Photos</a></li>
                <li><a href="/browse">Browse</a></li>
                <li><a href="/interests">Interests</a></li>
                <li><a href="/shortlist">Shortlist</a></li>
                <li><a href="/visitors">Visitors</a></li>
                <li><a href="/shortlisted-by">Shortlisted Me</a></li>
                <li><a href="/packages">Membership</a></li>
                <li><a href="/verification">Get Verified ✓</a></li>
                <li><a href="/billing">Billing</a></li>
                <li><a href="/messages">Messages</a></li>
                <li><a href="/settings">Settings</a></li>
                <li><a href="/logout">Sign out</a></li>
            </ul>
        </aside>

        <div>
            <div class="flex-between mb-3">
                <div>
                    <span class="eyebrow">Namaste,</span>
                    <h1 style="margin: 0;"><?= e(explode(' ', $me['name'])[0]) ?></h1>
                </div>
                <a href="/profile/edit" class="btn btn-ghost btn-sm">Edit Profile</a>
            </div>

            <!-- Activity Cross-Navigation Tabs -->
            <?php include __DIR__ . '/../partials/activity_tabs.php'; ?>

            <!-- Compact Mobile Membership Status Banner -->
            <div class="mobile-membership-pill mb-3">
                <div class="m-pill-info">
                    <span class="m-pill-badge">💎 <?= e($plan['name']) ?></span>
                    <span class="m-pill-expiry"><?= $membership['days_left'] === null ? 'Lifetime' : (int)$membership['days_left'] . ' days left' ?></span>
                </div>
                <a href="/packages" class="m-pill-link">Upgrade / Perks →</a>
            </div>

            <!-- Full Membership Card & Perks (Desktop Only - on mobile these live in Upgrade tab) -->
            <div class="desktop-membership-section">
                <div class="membership-card mb-4">
                    <div class="membership-main">
                        <span class="eyebrow">Membership</span>
                        <h2><?= e($plan['name']) ?></h2>
                        <p><?= e($plan['tagline'] ?? 'Your current membership benefits') ?></p>
                        <div class="membership-actions">
                            <a href="/packages" class="btn btn-gold btn-sm">Upgrade</a>
                            <a href="/packages" class="btn btn-ghost btn-sm">Renew</a>
                            <?php if ((int)$membership['boosts_left'] > 0): ?>
                                <form method="post" action="/boost" style="margin:0;">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-primary btn-sm">Use Boost</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="membership-metrics">
                        <div><span>Expiry</span><strong><?= $membership['expires_at'] ? e(date('M j, Y', strtotime($membership['expires_at']))) : 'Lifetime' ?></strong></div>
                        <div><span>Days Left</span><strong><?= $membership['days_left'] === null ? '-' : (int)$membership['days_left'] ?></strong></div>
                        <div><span>Contacts</span><strong><?= e($contactsText) ?></strong></div>
                        <div><span>Interests</span><strong><?= e($interestsText) ?></strong></div>
                        <div><span>Shortlists</span><strong><?= e($shortlistsText) ?></strong></div>
                        <div><span>Boosts</span><strong><?= (int)$membership['boosts_left'] ?></strong></div>
                        <div><span>Priority</span><strong><?= e($membership['priority_label']) ?></strong></div>
                        <div><span>Badge</span><strong><?= $membership['badge'] ? e($membership['badge']) : 'None' ?></strong></div>
                    </div>
                </div>

                <div class="member-perk-grid mb-4">
                    <div class="member-perk">
                        <strong>Profile Visitors</strong>
                        <span><?= plan_can($plan, 'see_who_viewed') ? 'Unlocked' : 'Starter Premium required' ?></span>
                        <a href="<?= plan_can($plan, 'see_who_viewed') ? '/visitors' : '/packages' ?>"><?= plan_can($plan, 'see_who_viewed') ? 'View visitors' : 'Upgrade' ?></a>
                    </div>
                    <div class="member-perk">
                        <strong>Who Shortlisted You</strong>
                        <span><?= plan_can($plan, 'see_who_shortlisted') ? (int)$stats['shortlisted_me'] . ' members' : 'Divine Plus required' ?></span>
                        <a href="<?= plan_can($plan, 'see_who_shortlisted') ? '/shortlisted-by' : '/packages' ?>"><?= plan_can($plan, 'see_who_shortlisted') ? 'View members' : 'Upgrade' ?></a>
                    </div>
                    <div class="member-perk">
                        <strong>Match Suggestions</strong>
                        <span><?= e($plan['match_suggestions'] ?? 'Basic') ?></span>
                        <a href="/browse">Browse matches</a>
                    </div>
                </div>
            </div>

            <!-- Activity Stat Cards -->
            <div class="stat-cards mb-4">
                <a href="/interests" class="stat-card" style="text-decoration:none; color:inherit;">
                    <div class="label">New Interests</div>
                    <div class="value"><?= (int)$stats['interests_received'] ?></div>
                </a>
                <a href="/messages" class="stat-card" style="text-decoration:none; color:inherit;">
                    <div class="label">Conversations</div>
                    <div class="value"><?= (int)$stats['interests_accepted'] ?></div>
                </a>
                <a href="/shortlist" class="stat-card" style="text-decoration:none; color:inherit;">
                    <div class="label">Shortlisted</div>
                    <div class="value"><?= (int)$stats['shortlisted'] ?></div>
                </a>
                <a href="/visitors" class="stat-card" style="text-decoration:none; color:inherit;">
                    <div class="label">Profile Views</div>
                    <div class="value"><?= (int)$stats['profile_views'] ?></div>
                </a>
            </div>

            <!-- Incoming Interests Feed -->
            <?php if ($recent_interests): ?>
            <div class="admin-card mb-4">
                <div class="flex-between mb-3">
                    <h3 style="margin: 0;">New interests for you</h3>
                    <a href="/interests" class="btn btn-ghost btn-sm">See all (<?= count($recent_interests) ?>) →</a>
                </div>
                <?php foreach ($recent_interests as $i): $age = age_from_dob($i['dob'] ?? null); ?>
                    <div class="flex-between" style="padding: .8rem 0; border-bottom: 1px solid var(--c-line); flex-wrap: wrap; gap: .6rem;">
                        <div>
                            <strong><a href="/member/<?= (int)$i['uid'] ?>"><?= e($i['name']) ?></a></strong><?php if ($age): ?>, <?= $age ?><?php endif; ?> sent you an interest
                            <div style="color: var(--c-muted); font-size: .85rem;">
                                <?php $meta = array_filter([$i['profession'] ?? null, $i['city'] ?? null]); ?>
                                <?php if ($meta): ?><?= e(implode(' - ', $meta)) ?> - <?php endif; ?>
                                <?= e(date('M j', strtotime($i['created_at']))) ?> -
                                <span class="pill <?= $i['status']==='accepted'?'green':($i['status']==='declined'?'red':'gold') ?>"><?= e($i['status']) ?></span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <?php if ($i['status'] === 'sent'): ?>
                                <form method="post" action="/interest/<?= (int)$i['id'] ?>/accept" style="display:inline;">
                                    <?= csrf_field() ?><button class="btn btn-primary btn-sm">Accept</button>
                                </form>
                                <form method="post" action="/interest/<?= (int)$i['id'] ?>/decline" style="display:inline;">
                                    <?= csrf_field() ?><button class="btn btn-ghost btn-sm">Decline</button>
                                </form>
                                <a href="/member/<?= (int)$i['uid'] ?>" class="btn btn-ghost btn-sm">View</a>
                            <?php elseif ($i['status'] === 'accepted'): ?>
                                <a href="/messages/<?= (int)$i['uid'] ?>" class="btn btn-primary btn-sm">Message</a>
                                <a href="/member/<?= (int)$i['uid'] ?>" class="btn btn-ghost btn-sm">View</a>
                            <?php else: ?>
                                <a href="/member/<?= (int)$i['uid'] ?>" class="btn btn-ghost btn-sm">View</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Discover Matches CTA Card (Replaces redundant card grid) -->
            <div class="admin-card text-center mb-4" style="padding: 2.2rem 1.5rem; background: linear-gradient(135deg, rgba(246, 193, 119, 0.15), rgba(212, 91, 122, 0.08)); border: 1.5px dashed rgba(212, 91, 122, 0.28); border-radius: var(--r-lg);">
                <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">🪷</div>
                <h3 style="font-family: var(--f-display); font-size: 1.4rem; color: var(--c-maroon); margin-bottom: 0.35rem;">Find Souls On Your Spiritual Path</h3>
                <p style="color: var(--c-muted); max-width: 480px; margin: 0 auto 1.25rem; font-size: 0.92rem;">Explore seekers filtered by spiritual tradition, dietary vows, guru lineage, and sacred lifestyle.</p>
                <a href="/browse" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.25rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    Explore Matches In Matches Tab →
                </a>
            </div>
        </div>
    </div>
</div>
</section>
