<?php /** @var array $rows, $page; @var int $total */ ?>
<section class="section-tight">
<div class="container">
    <div class="flex-between mb-3">
        <div>
            <span class="eyebrow">Find your partner</span>
            <h1 style="margin: 0;">Browse Seekers <span style="color: var(--c-muted); font-size: 1rem;">(<?= number_format($total) ?>)</span></h1>
        </div>
        <a href="/dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <form method="get" class="filters">
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
        <button class="btn btn-primary">Search</button>
    </form>

    <?php if (!$rows): ?>
        <div class="admin-card text-center" style="padding: 4rem 2rem;">
            <p style="font-family: var(--f-display); font-size: 1.4rem; color: var(--c-maroon);">No seekers found.</p>
            <p style="color: var(--c-muted);">Try widening your filters — or check back tomorrow. New seekers join every day.</p>
        </div>
    <?php else: ?>
        <div class="profiles-grid">
            <?php foreach ($rows as $m): $age = age_from_dob($m['dob']); $user = ['id' => $m['id'], 'name' => $m['name']]; ?>
                <article class="profile-card">
                    <div class="profile-photo">
                        <img src="<?= e(avatar_url($user)) ?>" alt="">
                        <?php if ($m['spiritual_path']): ?><span class="profile-badge"><?= e($m['spiritual_path']) ?></span><?php endif; ?>
                    </div>
                    <div class="profile-body">
                        <h3><?= e($m['name']) ?><?php if ($age): ?>, <?= $age ?><?php endif; ?></h3>
                        <div class="profile-meta"><?= e($m['profession'] ?: 'Seeker') ?> · <?= e($m['city']) ?></div>
                        <div class="profile-tags">
                            <?php if ($m['height_cm']): ?><span class="tag"><?= cm_to_feet((int)$m['height_cm']) ?></span><?php endif; ?>
                            <?php if ($m['community']): ?><span class="tag"><?= e($m['community']) ?></span><?php endif; ?>
                            <?php if ($m['guru']): ?><span class="tag tag-gold"><?= e($m['guru']) ?></span><?php endif; ?>
                        </div>
                        <p class="profile-about"><?= e($m['about_me'] ?? '') ?></p>
                        <a href="/member/<?= (int)$m['id'] ?>" class="btn btn-primary btn-sm">View Profile</a>
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
