<?php 
/** @var array $rows, $page, $viewerPlan; @var int $total; @var bool $advancedAllowed */ 
$selectedPath = trim((string)($_GET['path'] ?? ''));
$selectedOrg  = trim((string)($_GET['organization'] ?? ''));
$popularPaths = [
    'Advaita Vedanta'     => '🪷 Advaita Vedanta',
    'Bhakti Yoga'         => '🕉️ Bhakti Yoga',
    'Kriya Yoga'          => '🧘 Kriya Yoga',
    'Vipassana'           => '☸️ Vipassana',
    'Gaudiya Vaishnavism' => '🌸 Gaudiya Vaishnavism',
    'Kashmir Shaivism'    => '✨ Kashmir Shaivism',
    'Kundalini Yoga'      => '⚡ Kundalini Yoga',
    'Sikh Dharma'         => '🕊️ Sikh Dharma',
];
?>
<section class="section-tight">
<div class="container">
    <div class="flex-between mb-4" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <span class="eyebrow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                Spiritual Sangha
            </span>
            <h1 style="margin: 0;">Browse Seekers <span style="color: var(--c-muted); font-size: 1.1rem; font-weight: 500;">(<?= number_format($total) ?> souls)</span></h1>
        </div>
        <div style="display: flex; gap: .6rem;">
            <a href="/dashboard" class="btn btn-ghost btn-sm">Dashboard</a>
            <a href="/packages" class="btn btn-primary btn-sm">Membership Plans</a>
        </div>
    </div>

    <!-- QUICK SPIRITUAL PATH CHIPS -->
    <div class="spiritual-chips-bar" aria-label="Quick Spiritual Path Filters">
        <span class="spiritual-chips-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Popular Paths:
        </span>
        <?php
        $baseParams = $_GET;
        unset($baseParams['path'], $baseParams['page']);
        $allUrl = '/browse' . (!empty($baseParams) ? '?' . http_build_query($baseParams) : '');
        ?>
        <a href="<?= e($allUrl) ?>" class="spiritual-chip <?= ($selectedPath === '') ? 'is-active' : '' ?>">
            ✨ All Paths
        </a>
        <?php foreach ($popularPaths as $pKey => $pLabel): 
            $chipParams = $_GET;
            unset($chipParams['page']);
            $isActive = ($selectedPath === $pKey);
            if ($isActive) {
                unset($chipParams['path']);
            } else {
                $chipParams['path'] = $pKey;
            }
            $chipUrl = '/browse' . (!empty($chipParams) ? '?' . http_build_query($chipParams) : '');
        ?>
            <a href="<?= e($chipUrl) ?>" class="spiritual-chip <?= $isActive ? 'is-active' : '' ?>" title="<?= $isActive ? 'Click to remove filter' : 'Filter by ' . e($pKey) ?>">
                <?= $pLabel ?>
                <?php if ($isActive): ?>
                    <span class="chip-close" aria-hidden="true">×</span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- MOBILE FILTER TOGGLE (Jeevansathi-style) -->
    <button type="button" class="mobile-filter-toggle" id="mobileFilterToggle">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        Filters
        <?php
        $activeFilters = 0;
        foreach (['city','religion','path','diet','min_age','max_age','education','profession','community','guru','organization','temple_frequency','scripture','lifestyle','min_height','max_height','vegetarian','vegan','no_smoking','no_alcohol'] as $_f) {
            if (!empty($_GET[$_f])) $activeFilters++;
        }
        if ($activeFilters > 0): ?>
            <span class="filter-count-badge"><?= $activeFilters ?></span>
        <?php endif; ?>
    </button>

    <!-- GLASS FILTER PANEL -->
    <form method="get" class="filters filters-wide mobile-filters-collapsible">
        <div class="field"><label>City / Location</label><input type="text" name="city" value="<?= e($_GET['city'] ?? '') ?>" placeholder="Any city"></div>
        <div class="field"><label>Religion</label><input type="text" name="religion" value="<?= e($_GET['religion'] ?? '') ?>" placeholder="Hindu, Buddhist..."></div>
        <div class="field spiritual-path-filter-field">
            <label>Spiritual Path</label>
            <div class="custom-combobox" id="browse_path_combobox" data-placeholder="All Spiritual Paths">
                <input type="hidden" name="path" id="browse_path_val" value="<?= e($selectedPath) ?>">
                <div class="combobox-trigger" id="browse_path_trigger" tabindex="0" role="combobox" aria-expanded="false" aria-haspopup="listbox" title="Search or select spiritual path">
                    <span class="combobox-text <?= ($selectedPath === '') ? 'is-placeholder' : '' ?>">
                        <?= $selectedPath !== '' ? '🪷 ' . e($selectedPath) : 'All Spiritual Paths' ?>
                    </span>
                    <div class="combobox-icons">
                        <button type="button" class="combobox-clear" id="browse_path_clear" title="Clear path selection" style="<?= ($selectedPath !== '') ? 'display:inline-flex;' : 'display:none;' ?>">×</button>
                        <svg class="combobox-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 9l6 6 6-6"/></svg>
                    </div>
                </div>

                <div class="combobox-dropdown" id="browse_path_dropdown" role="listbox">
                    <div class="combobox-search-box">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" class="combobox-search-input" id="browse_path_search" placeholder="Search 37+ traditions..." autocomplete="off">
                    </div>
                    <div class="combobox-options-list" id="browse_path_options">
                        <div class="combobox-option <?= ($selectedPath === '') ? 'is-selected' : '' ?>" data-value="" data-label="All Spiritual Paths" role="option">
                            <span class="combobox-opt-content"><span class="combobox-opt-icon">✨</span> <em>All Spiritual Paths (Any)</em></span>
                        </div>
                        <div class="combobox-divider"></div>
                        <?php foreach (spiritual_paths() as $p): ?>
                            <div class="combobox-option <?= ($selectedPath === $p) ? 'is-selected' : '' ?>" data-value="<?= e($p) ?>" data-label="🪷 <?= e($p) ?>" role="option">
                                <span class="combobox-opt-content"><span class="combobox-opt-icon">🪷</span> <?= e($p) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="combobox-empty" id="browse_path_empty" style="display: none;">
                            <div>No matching spiritual paths found</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="field"><label>Dietary Practice</label>
            <select name="diet">
                <option value="">Any Diet</option>
                <?php foreach (['vegetarian','sattvic','vegan','eggetarian','non_vegetarian','jain'] as $d): ?>
                    <option value="<?= $d ?>" <?= ($_GET['diet'] ?? '')===$d ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ', $d)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field"><label>Min Age</label><input type="number" name="min_age" min="18" max="80" value="<?= e($_GET['min_age'] ?? '') ?>" placeholder="18"></div>
        <div class="field"><label>Max Age</label><input type="number" name="max_age" min="18" max="80" value="<?= e($_GET['max_age'] ?? '') ?>" placeholder="Any"></div>

        <?php if ($advancedAllowed): ?>
            <div class="field"><label>Education</label><input type="text" name="education" value="<?= e($_GET['education'] ?? '') ?>"></div>
            <div class="field"><label>Profession</label><input type="text" name="profession" value="<?= e($_GET['profession'] ?? '') ?>"></div>
            <div class="field"><label>Community</label><input type="text" name="community" value="<?= e($_GET['community'] ?? '') ?>"></div>
            <div class="field"><label>Guru / Lineage</label><input type="text" name="guru" value="<?= e($_GET['guru'] ?? '') ?>"></div>
            <div class="field spiritual-org-filter-field">
                <label>Spiritual Org</label>
                <div class="custom-combobox" id="browse_org_combobox" data-placeholder="All Organizations">
                    <input type="hidden" name="organization" id="browse_org_val" value="<?= e($selectedOrg) ?>">
                    <div class="combobox-trigger" id="browse_org_trigger" tabindex="0" role="combobox" aria-expanded="false" aria-haspopup="listbox" title="Search or select spiritual organization">
                        <span class="combobox-text <?= ($selectedOrg === '') ? 'is-placeholder' : '' ?>">
                            <?= $selectedOrg !== '' ? '🏛️ ' . e($selectedOrg) : 'All Organizations' ?>
                        </span>
                        <div class="combobox-icons">
                            <button type="button" class="combobox-clear" id="browse_org_clear" title="Clear organization selection" style="<?= ($selectedOrg !== '') ? 'display:inline-flex;' : 'display:none;' ?>">×</button>
                            <svg class="combobox-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                    </div>

                    <div class="combobox-dropdown" id="browse_org_dropdown" role="listbox">
                        <div class="combobox-search-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" class="combobox-search-input" id="browse_org_search" placeholder="Search 40+ organizations..." autocomplete="off">
                        </div>
                        <div class="combobox-options-list" id="browse_org_options">
                            <div class="combobox-option <?= ($selectedOrg === '') ? 'is-selected' : '' ?>" data-value="" data-label="All Organizations" role="option">
                                <span class="combobox-opt-content"><span class="combobox-opt-icon">✨</span> <em>All Organizations (Any)</em></span>
                            </div>
                            <div class="combobox-divider"></div>
                            <?php foreach (spiritual_organizations() as $orgItem): ?>
                                <div class="combobox-option <?= ($selectedOrg === $orgItem) ? 'is-selected' : '' ?>" data-value="<?= e($orgItem) ?>" data-label="🏛️ <?= e($orgItem) ?>" role="option">
                                    <span class="combobox-opt-content"><span class="combobox-opt-icon">🏛️</span> <?= e($orgItem) ?></span>
                                </div>
                            <?php endforeach; ?>
                            <div class="combobox-empty" id="browse_org_empty" style="display: none;">
                                <div>No matching organizations found</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            <div class="field"><label>Min Height (cm)</label><input type="number" name="min_height" min="120" max="220" value="<?= e($_GET['min_height'] ?? '') ?>"></div>
            <div class="field"><label>Max Height (cm)</label><input type="number" name="max_height" min="120" max="220" value="<?= e($_GET['max_height'] ?? '') ?>"></div>
            <label class="filter-check"><input type="checkbox" name="vegetarian" value="1" <?= !empty($_GET['vegetarian']) ? 'checked' : '' ?>> Vegetarian</label>
            <label class="filter-check"><input type="checkbox" name="vegan" value="1" <?= !empty($_GET['vegan']) ? 'checked' : '' ?>> Vegan</label>
            <label class="filter-check"><input type="checkbox" name="no_smoking" value="1" <?= !empty($_GET['no_smoking']) ? 'checked' : '' ?>> No smoking</label>
            <label class="filter-check"><input type="checkbox" name="no_alcohol" value="1" <?= !empty($_GET['no_alcohol']) ? 'checked' : '' ?>> No alcohol</label>
        <?php else: ?>
            <div class="filter-upgrade">
                <div>
                    <strong>Advanced filters locked</strong><br>
                    <span>Divine Plus unlocks guru, lifestyle, height, organization, and deeper spiritual filters.</span>
                </div>
                <a href="/packages" class="btn btn-gold btn-sm">Upgrade</a>
            </div>
        <?php endif; ?>

        <button class="btn btn-primary" style="height: 48px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            Search
        </button>
    </form>

    <?php if (!$rows): ?>
        <div class="admin-card text-center" style="padding: 4.5rem 2rem;">
            <div style="font-size: 2.8rem; margin-bottom: 1rem;">🪷</div>
            <h3 style="font-family: var(--f-display); font-size: 1.8rem; color: var(--c-maroon); margin-bottom: .4rem;">No seekers match your criteria</h3>
            <p style="color: var(--c-muted); max-width: 480px; margin: 0 auto 1.5rem;">Try widening your location, age, or dietary filters, or check back soon as new sincere seekers join daily.</p>
            <a href="/browse" class="btn btn-ghost btn-sm">Clear All Filters</a>
        </div>
    <?php else: ?>
        <div class="profiles-grid">
            <?php foreach ($rows as $m): $age = age_from_dob($m['dob']); $user = ['id' => $m['id'], 'name' => $m['name']]; ?>
                <article class="profile-card is-clickable" onclick="navigateToProfile(event, '/member/<?= (int)$m['id'] ?>')" tabindex="0" role="button" aria-label="View profile of <?= e($m['name']) ?>">
                    <div class="profile-photo">
                        <img src="<?= e(avatar_url($user)) ?>" alt="<?= e($m['name']) ?>" loading="lazy">
                        <div class="photo-gradient-overlay"></div>
                        
                        <!-- Top Badges -->
                        <div class="photo-top-badges">
                            <?php if (!empty($m['spiritual_path'])): ?>
                                <span class="profile-badge-path">🪷 <?= e($m['spiritual_path']) ?></span>
                            <?php else: ?>
                                <span></span>
                            <?php endif; ?>
                            <?php if (!empty($m['is_boosted'])): ?>
                                <span class="profile-badge-status status-boosted">⚡ Boosted</span>
                            <?php elseif (!empty($m['is_featured'])): ?>
                                <span class="profile-badge-status status-featured">★ Featured</span>
                            <?php endif; ?>
                        </div>

                        <!-- Floating Info Overlay at bottom of photo -->
                        <div class="photo-bottom-info">
                            <div class="photo-name-row">
                                <h3 class="photo-name"><?= e($m['name']) ?><?php if ($age): ?>, <?= $age ?><?php endif; ?></h3>
                                <?= verified_badge($m['verified_tier'] ?? null, 'sm') ?>
                                <?php if (!empty($m['premium_badge'])): ?>
                                    <span class="pill gold" style="font-size: .7rem; padding: .1rem .5rem;"><?= e($m['plan_name'] ?? 'Premium') ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="photo-sub">
                                <?= e($m['profession'] ?: 'Seeker') ?> · <?= e($m['city'] ?: '-') ?>
                            </div>
                        </div>
                    </div>

                    <div class="profile-body">
                        <!-- Spiritual & Lifestyle Highlights -->
                        <div class="profile-tags">
                            <?php if (!empty($m['diet'])): ?><span class="tag tag-gold">🌿 <?= ucfirst(e($m['diet'])) ?></span><?php endif; ?>
                            <?php if (!empty($m['guru'])): ?><span class="tag">🕉️ <?= e($m['guru']) ?></span><?php endif; ?>
                            <?php if (!empty($m['height_cm'])): ?><span class="tag">📏 <?= cm_to_feet((int)$m['height_cm']) ?></span><?php endif; ?>
                            <?php if (!empty($m['community'])): ?><span class="tag"><?= e($m['community']) ?></span><?php endif; ?>
                        </div>

                        <?php if (!empty($m['about_me'])): ?>
                            <p class="profile-about"><?= e($m['about_me']) ?></p>
                        <?php endif; ?>

                        <!-- 4 Synchronized Action Buttons (Jeevansathi Style) -->
                        <div class="profile-card-actions">
                            <!-- 1. Interest -->
                            <div class="card-action-item">
                                <form method="post" action="/interest/send/<?= (int)$m['id'] ?>" style="margin:0;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="card-action-circle card-action-interest" title="Express Interest" aria-label="Express Interest">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                                        </svg>
                                    </button>
                                </form>
                                <span class="card-action-label">Interest</span>
                            </div>

                            <!-- 2. Shortlist -->
                            <div class="card-action-item">
                                <form method="post" action="/shortlist/<?= (int)$m['id'] ?>" style="margin:0;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="card-action-circle card-action-shortlist" title="Shortlist Profile" aria-label="Shortlist Profile">
                                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    </button>
                                </form>
                                <span class="card-action-label">Shortlist</span>
                            </div>

                            <!-- 3. Ignore -->
                            <div class="card-action-item">
                                <button type="button" class="card-action-circle card-action-ignore" title="Ignore / Pass" aria-label="Ignore / Pass" onclick="dismissProfileCard(this)">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                                <span class="card-action-label">Ignore</span>
                            </div>

                            <!-- 4. Chat -->
                            <div class="card-action-item">
                                <a href="/messages/<?= (int)$m['id'] ?>" class="card-action-circle card-action-chat" title="Chat" aria-label="Chat">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                    </svg>
                                </a>
                                <span class="card-action-label">Chat</span>
                            </div>
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
