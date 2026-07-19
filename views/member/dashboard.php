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

            <div class="stat-cards">
                <div class="stat-card"><div class="label">New Interests</div><div class="value"><?= (int)$stats['interests_received'] ?></div></div>
                <div class="stat-card"><div class="label">Conversations</div><div class="value"><?= (int)$stats['interests_accepted'] ?></div></div>
                <div class="stat-card"><div class="label">Shortlisted</div><div class="value"><?= (int)$stats['shortlisted'] ?></div></div>
                <div class="stat-card"><div class="label">Profile Views</div><div class="value"><?= (int)$stats['profile_views'] ?></div></div>
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

            <?php if ($recent_interests): ?>
            <div class="admin-card mb-4">
                <h3 style="margin-bottom: 1rem;">New interests for you</h3>
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
                <div class="mt-2"><a href="/interests">See all interests</a></div>
            </div>
            <?php endif; ?>

            <?php if ($matches): ?>
            <h2 style="font-size: 1.6rem; margin-bottom: 1rem;">Suggested for you</h2>
            <div class="profiles-grid">
                <?php foreach ($matches as $m): $age = age_from_dob($m['dob']); ?>
                    <article class="profile-card">
                        <div class="profile-photo">
                            <img src="<?= e(avatar_url($m)) ?>" alt="">
                            <?php if ($m['spiritual_path']): ?><span class="profile-badge"><?= e($m['spiritual_path']) ?></span><?php endif; ?>
                            <?php if (!empty($m['is_boosted'])): ?><span class="profile-badge profile-badge-right">Boosted</span><?php elseif (!empty($m['is_featured'])): ?><span class="profile-badge profile-badge-right">Featured</span><?php endif; ?>
                        </div>
                        <div class="profile-body">
                            <h3><?= e($m['name']) ?><?php if ($age): ?>, <?= $age ?><?php endif; ?></h3>
                            <?php if (!empty($m['premium_badge'])): ?>
                                <span class="pill gold" style="font-size: .75rem; margin-bottom: .35rem;"><?= e($m['plan_name']) ?></span>
                            <?php endif; ?>
                            <?= verified_badge($m['verified_tier'] ?? null, 'sm') ?>
                            <?php if (empty($m['profile_complete'])): ?>
                                <span class="pill gold" style="font-size: .75rem; margin-bottom: .35rem;">Profile in progress</span>
                            <?php endif; ?>
                            <div class="profile-meta"><?= e($m['profession'] ?: 'Seeker') ?> - <?= e($m['city'] ?: '-') ?></div>
                            <p class="profile-about"><?= e($m['about_me'] ?? '') ?></p>
                            <div class="profile-card-actions">
                                <a href="/member/<?= (int)$m['id'] ?>" class="btn btn-ghost btn-sm">View Profile</a>
                                <form method="post" action="/interest/send/<?= (int)$m['id'] ?>">
                                    <?= csrf_field() ?><button class="btn btn-primary btn-sm">Express Interest</button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</section>
