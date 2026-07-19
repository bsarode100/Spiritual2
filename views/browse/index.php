<?php /** @var array $rows, $page, $viewerPlan; @var int $total; @var bool $advancedAllowed */ ?>
<section class="section-tight">
<div class="container">
    <div class="flex-between mb-3">
        <div>
            <span class="eyebrow">Find your partner</span>
            <h1 style="margin: 0;">Browse Seekers <span style="color: var(--c-muted); font-size: 1rem;">(<?= number_format($total) ?>)</span></h1>
        </div>
        <a href="/dashboard" class="btn btn-ghost btn-sm">Dashboard</a>
    </div>

    <form method="get" class="filters filters-wide">
        <div class="field"><label>City</label><input type="text" name="city" value="<?= e($_GET['city'] ?? '') ?>" placeholder="Any city"></div>
        <div class="field"><label>Religion</label><input type="text" name="religion" value="<?= e($_GET['religion'] ?? '') ?>" placeholder="Hindu, Buddhist..."></div>
        <div class="field"><label>Spiritual Path</label><input type="text" name="path" value="<?= e($_GET['path'] ?? '') ?>" placeholder="ISKCON, Vipassana..."></div>
        <div class="field"><label>Diet</label>
            <select name="diet">
                <option value="">Any</option>
                <?php foreach (['vegetarian','sattvic','vegan','eggetarian','non_vegetarian','jain'] as $d): ?>
                    <option value="<?= $d ?>" <?= ($_GET['diet'] ?? '')===$d ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ', $d)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field"><label>Min Age</label><input type="number" name="min_age" min="18" max="80" value="<?= e($_GET['min_age'] ?? '') ?>"></div>
        <div class="field"><label>Max Age</label><input type="number" name="max_age" min="18" max="80" value="<?= e($_GET['max_age'] ?? '') ?>"></div>

        <?php if ($advancedAllowed): ?>
            <div class="field"><label>Education</label><input type="text" name="education" value="<?= e($_GET['education'] ?? '') ?>"></div>
            <div class="field"><label>Profession</label><input type="text" name="profession" value="<?= e($_GET['profession'] ?? '') ?>"></div>
            <div class="field"><label>Community</label><input type="text" name="community" value="<?= e($_GET['community'] ?? '') ?>"></div>
            <div class="field"><label>Guru</label><input type="text" name="guru" value="<?= e($_GET['guru'] ?? '') ?>"></div>
            <div class="field"><label>Organization</label><input type="text" name="organization" value="<?= e($_GET['organization'] ?? '') ?>"></div>
            <div class="field"><label>Temple Visits</label>
                <select name="temple_frequency">
                    <option value="">Any</option>
                    <?php foreach (['Daily','Weekly','Monthly','Occasionally'] as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= ($_GET['temple_frequency'] ?? '')===$opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label>Scripture</label><input type="text" name="scripture" value="<?= e($_GET['scripture'] ?? '') ?>"></div>
            <div class="field"><label>Lifestyle</label><input type="text" name="lifestyle" value="<?= e($_GET['lifestyle'] ?? '') ?>"></div>
            <div class="field"><label>Min Height</label><input type="number" name="min_height" min="120" max="220" value="<?= e($_GET['min_height'] ?? '') ?>"></div>
            <div class="field"><label>Max Height</label><input type="number" name="max_height" min="120" max="220" value="<?= e($_GET['max_height'] ?? '') ?>"></div>
            <label class="filter-check"><input type="checkbox" name="vegetarian" value="1" <?= !empty($_GET['vegetarian']) ? 'checked' : '' ?>> Vegetarian</label>
            <label class="filter-check"><input type="checkbox" name="vegan" value="1" <?= !empty($_GET['vegan']) ? 'checked' : '' ?>> Vegan</label>
            <label class="filter-check"><input type="checkbox" name="no_smoking" value="1" <?= !empty($_GET['no_smoking']) ? 'checked' : '' ?>> No smoking</label>
            <label class="filter-check"><input type="checkbox" name="no_alcohol" value="1" <?= !empty($_GET['no_alcohol']) ? 'checked' : '' ?>> No alcohol</label>
        <?php else: ?>
            <div class="filter-upgrade">
                <strong>Advanced filters locked</strong>
                <span>Divine Plus unlocks guru, lifestyle, height, organization, and deeper spiritual filters.</span>
                <a href="/packages">Upgrade</a>
            </div>
        <?php endif; ?>

        <button class="btn btn-primary">Search</button>
    </form>

    <?php if (!$rows): ?>
        <div class="admin-card text-center" style="padding: 4rem 2rem;">
            <p style="font-family: var(--f-display); font-size: 1.4rem; color: var(--c-maroon);">No seekers found.</p>
            <p style="color: var(--c-muted);">Try widening your filters, or check back later as new seekers join.</p>
        </div>
    <?php else: ?>
        <div class="profiles-grid">
            <?php foreach ($rows as $m): $age = age_from_dob($m['dob']); $user = ['id' => $m['id'], 'name' => $m['name']]; ?>
                <article class="profile-card">
                    <div class="profile-photo">
                        <img src="<?= e(avatar_url($user)) ?>" alt="">
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
                        <div class="profile-tags">
                            <?php if ($m['height_cm']): ?><span class="tag"><?= cm_to_feet((int)$m['height_cm']) ?></span><?php endif; ?>
                            <?php if ($m['community']): ?><span class="tag"><?= e($m['community']) ?></span><?php endif; ?>
                            <?php if ($m['guru']): ?><span class="tag tag-gold"><?= e($m['guru']) ?></span><?php endif; ?>
                        </div>
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

        <?php if ($page['pages'] > 1): ?>
            <div class="pager">
                <?php for ($i = 1; $i <= $page['pages']; $i++):
                    $q = $_GET; $q['page'] = $i; $url = '/browse?' . http_build_query($q); ?>
                    <?php if ($i === $page['page']): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= e($url) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</section>
