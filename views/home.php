<?php /** @var array $featured, $stories, $packages, $posts */ ?>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><path d="M12 3v18M3 12h18M6 6l12 12M6 18L18 6"/></svg>
                    Sincere Seekers · Sacred Unions
                </span>
                <h1>
                    <?= e(setting('hero_heading','Find a partner who walks your')) ?>
                    <span class="gold-accent">spiritual path</span>
                </h1>
                <p class="hero-sub">
                    <strong>More Than a Match. A Shared Journey.</strong><br>
                    <?= e(setting('hero_subheading','A sacred space for sincere seekers to find a life-companion rooted in dharma, sadhana, and love.')) ?>
                </p>
                <div class="hero-cta">
                    <a href="/register" class="btn btn-primary btn-lg">
                        <?= e(setting('hero_cta_text','Begin Your Journey')) ?>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="/browse" class="btn btn-ghost btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        Browse Seekers
                    </a>
                </div>
                <div class="hero-trust">
                    <div class="avatars">
                        <div>🪷</div><div>✨</div><div>🌿</div><div>🕊️</div>
                    </div>
                    <div><strong style="color: var(--c-ink);"><?= e(setting('stat_marriages','1,200+')) ?> sacred unions</strong> · across <?= e(setting('stat_paths','18')) ?> spiritual paths</div>
                </div>
            </div>

            <div class="hero-visual">
                <!-- Background sacred geometry mandala -->
                <svg class="mandala" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <g fill="none" stroke="#D99420" stroke-width="0.45" opacity="0.6">
                        <circle cx="100" cy="100" r="95"/>
                        <circle cx="100" cy="100" r="78"/>
                        <circle cx="100" cy="100" r="60"/>
                        <circle cx="100" cy="100" r="42"/>
                        <circle cx="100" cy="100" r="24"/>
                        <?php for ($i = 0; $i < 16; $i++) {
                            $angle = $i * 22.5;
                            echo "<line x1='100' y1='100' x2='100' y2='5' transform='rotate($angle 100 100)'/>";
                        } ?>
                    </g>
                </svg>

                <!-- Card 3 (back small) -->
                <div class="hero-card hero-card-3">
                    <div>
                        <div class="script" style="color: var(--c-saffron); font-size: 2.2rem;">सहजीवन</div>
                        <div style="font-size: .88rem; font-weight: 600; color: var(--c-maroon); letter-spacing: .04em; margin-top: .2rem;">Walk together</div>
                    </div>
                </div>

                <!-- Card 2 (medium - groom sadhak) -->
                <div class="hero-card hero-card-2">
                    <div style="height: 100%; background: linear-gradient(135deg, rgba(235, 168, 64, 0.95) 0%, rgba(217, 107, 123, 0.95) 100%); display: flex; align-items: flex-end; padding: 1.5rem; color: white;">
                        <div>
                            <div style="font-size: .72rem; letter-spacing: .18em; opacity: .95; margin-bottom: .25rem; font-weight: 700; text-transform: uppercase;">VIPASSANA · 6 YRS</div>
                            <div style="font-family: var(--f-display); font-size: 1.55rem; font-weight: 600; line-height: 1.15;">Arjun, 31</div>
                            <div style="font-size: .86rem; opacity: .92;">Software Engineer · Bengaluru</div>
                        </div>
                    </div>
                </div>

                <!-- Card 1 (main, front - bride sadhika) -->
                <div class="hero-card hero-card-main">
                    <div style="height: 100%; background: linear-gradient(145deg, #FFFDF8 0%, #FCECEF 60%, #F8E5EC 100%); position: relative; overflow: hidden;">
                        <svg viewBox="0 0 200 240" style="width: 100%; height: 100%;" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="auroraLight" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FDF3E5"/>
                                    <stop offset="50%" stop-color="#FCEBEF"/>
                                    <stop offset="100%" stop-color="#EFE6F7"/>
                                </linearGradient>
                            </defs>
                            <rect width="200" height="240" fill="url(#auroraLight)"/>
                            <!-- Stylized serene portrait profile -->
                            <circle cx="100" cy="85" r="38" fill="#FFFDF8" opacity=".95"/>
                            <path d="M38 240 Q40 135 100 135 Q160 135 162 240 Z" fill="#FFFDF8" opacity=".95"/>
                            <!-- Auspicious kumkum bindi -->
                            <circle cx="100" cy="72" r="3.6" fill="#D96B7B"/>
                            <!-- Subtle lotus aura arc -->
                            <path d="M70 110 Q100 125 130 110" stroke="#D99420" stroke-width="1.2" fill="none" opacity=".65"/>
                        </svg>
                    </div>
                    <div class="hero-card-badge">
                        <h4>Anjali, 28</h4>
                        <p>Yoga Teacher · Bhakti Path · Pune</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PROMINENT QUICK MATCH / SEARCH GLASS PANEL -->
<div class="container hero-search-glass">
    <div class="glass-search-panel">
        <div class="glass-search-header">
            <h3 class="glass-search-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--c-saffron);"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                Find Your Spiritual Life Partner
            </h3>
            <span class="glass-search-tagline">Filter by spiritual path, sadhana, lifestyle, and values</span>
        </div>
        <form method="get" action="/browse" class="glass-search-grid">
            <div class="glass-field">
                <label>Spiritual Path</label>
                <input type="text" name="path" placeholder="e.g. ISKCON, Vipassana" class="glass-control">
            </div>
            <div class="glass-field">
                <label>Dietary Practice</label>
                <select name="diet" class="glass-control">
                    <option value="">Any Diet</option>
                    <option value="sattvic">Sattvic</option>
                    <option value="vegetarian">Vegetarian</option>
                    <option value="vegan">Vegan</option>
                    <option value="jain">Jain</option>
                    <option value="eggetarian">Eggetarian</option>
                </select>
            </div>
            <div class="glass-field">
                <label>City / Location</label>
                <input type="text" name="city" placeholder="Any City" class="glass-control">
            </div>
            <div class="glass-field">
                <label>Min Age</label>
                <select name="min_age" class="glass-control">
                    <option value="">18 Yrs</option>
                    <option value="21">21 Yrs</option>
                    <option value="24">24 Yrs</option>
                    <option value="27">27 Yrs</option>
                    <option value="30">30 Yrs</option>
                    <option value="35">35 Yrs</option>
                </select>
            </div>
            <div class="glass-field">
                <label>Max Age</label>
                <select name="max_age" class="glass-control">
                    <option value="">Any Age</option>
                    <option value="28">28 Yrs</option>
                    <option value="32">32 Yrs</option>
                    <option value="36">36 Yrs</option>
                    <option value="42">42 Yrs</option>
                    <option value="50">50 Yrs</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="width: 100%; height: 48px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- STATS -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div><div class="stat-num"><?= e(setting('stat_members','25,000+')) ?></div><div class="stat-label">Sincere Seekers</div></div>
            <div><div class="stat-num"><?= e(setting('stat_marriages','1,200+')) ?></div><div class="stat-label">Sacred Unions</div></div>
            <div><div class="stat-num"><?= e(setting('stat_paths','18')) ?></div><div class="stat-label">Spiritual Paths</div></div>
            <div><div class="stat-num"><?= e(setting('stat_countries','40+')) ?></div><div class="stat-label">Countries Worldwide</div></div>
        </div>
    </div>
</section>

<!-- WHY US / PHILOSOPHY -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Shared Values · Sincere Dharma</span>
            <h2>Not just bio-data. <em style="color: var(--c-saffron); font-family: var(--f-display);">Dharma-data.</em></h2>
            <p class="lead">Traditional matrimony platforms prioritize superficial metrics. We ask about your sadhana, your guru, your ishta-devata, and the spiritual rhythm of your life — because shared values are the foundation of a peaceful home.</p>
        </div>

        <div class="features-grid">
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L4 7v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V7l-8-5z"/></svg>
                </div>
                <h3>Verified Seekers</h3>
                <p>Every profile is reviewed by our team with optional government ID & live selfie verification. A sincere, trusted community without fake claims or noise.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>
                </div>
                <h3>Spiritual Compatibility</h3>
                <p>Filter by lineage, guru, meditation practice, diet, and lifestyle. Discover partners who walk your specific spiritual path with sincerity.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </div>
                <h3>Dharmic Grihastha</h3>
                <p>Created for seekers who revere marriage as sacred sadhana — respectful matchmaking, family involvement, and mindful communication.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                </div>
                <h3>Meaningful Dialogue</h3>
                <p>Once an interest is mutually accepted, converse directly. Deep spiritual companionship begins with honest, respectful understanding.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3>Privacy & Reverence</h3>
                <p>Your photos and contact details stay protected. You maintain complete control over who views your full profile and details at all times.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>Concierge Guidance</h3>
                <p>Sangam premium members receive personal matchmaking guidance from our experienced team, deeply familiar with our spiritual seekers.</p>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED PROFILES -->
<?php if ($featured): ?>
<section class="section section-soft">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Soul Companions</span>
            <h2>Recent <em style="color: var(--c-saffron); font-family: var(--f-display);">seekers</em></h2>
            <p class="lead">A few sincere seekers who have recently joined our sangha. Discover profiles, explore compatibility, and begin a meaningful connection.</p>
        </div>
        <div class="profiles-grid">
            <?php foreach (array_slice($featured, 0, 6) as $m):
                $age = age_from_dob($m['dob']); ?>
                <article class="profile-card">
                    <div class="profile-photo">
                        <img src="<?= e(avatar_url($m)) ?>" alt="<?= e($m['name']) ?>" loading="lazy">
                        <?php if (!empty($m['spiritual_path'])): ?>
                            <span class="profile-badge"><?= e($m['spiritual_path']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="profile-body">
                        <h3><?= e($m['name']) ?><?php if ($age): ?>, <?= $age ?><?php endif; ?></h3>
                        <div style="margin-bottom: .6rem;">
                            <?= verified_badge($m['verified_tier'] ?? null, 'sm') ?>
                        </div>
                        <div class="profile-meta">
                            <?= e($m['profession'] ?: 'Seeker') ?> · <?= e(trim($m['city'] . ' · ' . $m['state'], ' ·')) ?>
                        </div>
                        <div class="profile-tags">
                            <?php if (!empty($m['height_cm'])): ?><span class="tag"><?= cm_to_feet((int)$m['height_cm']) ?></span><?php endif; ?>
                            <?php if (!empty($m['education'])): ?><span class="tag"><?= e($m['education']) ?></span><?php endif; ?>
                            <?php if (!empty($m['diet'])): ?><span class="tag tag-gold"><?= ucfirst(e($m['diet'])) ?></span><?php endif; ?>
                        </div>
                        <p class="profile-about"><?= e($m['about_me'] ?? '') ?></p>
                        <div class="profile-card-actions">
                            <a href="/member/<?= (int)$m['id'] ?>" class="btn btn-ghost btn-sm" style="flex: 1;">View Profile</a>
                            <form method="post" action="/interest/send/<?= (int)$m['id'] ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-primary btn-sm" title="Express Interest">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                    Connect
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/browse" class="btn btn-primary btn-lg">
                Browse All Seekers
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- HOW IT WORKS -->
<section class="section section-radiant">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">A Sacred Pathway</span>
            <h2>Three steps to <em style="font-family: var(--f-display); color: var(--c-saffron);">grihastha dharma</em></h2>
            <p class="lead">From sincere inner intention to a shared lifetime of devotion and companionship.</p>
        </div>
        <div class="features-grid">
            <div class="feature">
                <div style="width: 58px; height: 58px; border-radius: 20px; background: linear-gradient(135deg, #E5A534, #D47A22); color: #fff; display: flex; align-items: center; justify-content: center; font-family: var(--f-display); font-size: 1.8rem; font-weight: 700; margin-bottom: 1.5rem; box-shadow: 0 6px 18px rgba(217,148,32,.25);">१</div>
                <h3 style="color: var(--c-maroon);">Declare Your Sankalpa</h3>
                <p>Join free. Create a profile that expresses your authentic path — your daily sadhana, your guru, your values, and the qualities your heart yearns for.</p>
            </div>
            <div class="feature">
                <div style="width: 58px; height: 58px; border-radius: 20px; background: linear-gradient(135deg, #E5A534, #D47A22); color: #fff; display: flex; align-items: center; justify-content: center; font-family: var(--f-display); font-size: 1.8rem; font-weight: 700; margin-bottom: 1.5rem; box-shadow: 0 6px 18px rgba(217,148,32,.25);">२</div>
                <h3 style="color: var(--c-maroon);">Discover Compatible Sangha</h3>
                <p>Browse, filter, and shortlist profiles. Deep spiritual filters help you find souls dedicated to your specific lineage and way of life.</p>
            </div>
            <div class="feature">
                <div style="width: 58px; height: 58px; border-radius: 20px; background: linear-gradient(135deg, #E5A534, #D47A22); color: #fff; display: flex; align-items: center; justify-content: center; font-family: var(--f-display); font-size: 1.8rem; font-weight: 700; margin-bottom: 1.5rem; box-shadow: 0 6px 18px rgba(217,148,32,.25);">३</div>
                <h3 style="color: var(--c-maroon);">Walk the Path Together</h3>
                <p>Express interest. Once mutual, converse with reverence, involve families, and step forward into a sacred matrimonial union.</p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="/register" class="btn btn-primary btn-lg">Begin Your Sacred Journey · Free</a>
        </div>
    </div>
</section>

<!-- HAPPY STORIES -->
<?php if ($stories): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Sacred Unions</span>
            <h2>Happy <em style="color: var(--c-saffron); font-family: var(--f-display);">stories</em></h2>
            <p class="lead">Souls who recognized each other here, and now nurture dharma together.</p>
        </div>
        <div class="features-grid">
            <?php foreach (array_slice($stories, 0, 3) as $s): ?>
                <div class="story-card">
                    <p><?= e($s['story']) ?></p>
                    <div class="story-couple">
                        <div class="story-couple-avatar">💞</div>
                        <div>
                            <div class="story-couple-name"><?= e($s['couple_name']) ?></div>
                            <?php if (!empty($s['married_on'])): ?>
                                <div class="story-couple-date">Married <?= date('M Y', strtotime($s['married_on'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/happy-stories" class="btn btn-ghost">Read More Sacred Stories →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- PACKAGES -->
<?php if ($packages):
    $orderSlugs = ['free','starter','divine','soul_elite','eternal'];
    $bySlug = [];
    foreach ($packages as $p) { if (!empty($p['slug'])) $bySlug[$p['slug']] = $p; }
    $homeOrdered = [];
    foreach ($orderSlugs as $s) if (isset($bySlug[$s])) $homeOrdered[] = $bySlug[$s];
    foreach ($packages as $p) { if (empty($p['slug']) || !in_array($p['slug'], $orderSlugs, true)) $homeOrdered[] = $p; }
?>
<section class="section section-soft">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Transparent Membership</span>
            <h2>Membership paths for every <em style="color: var(--c-saffron); font-family: var(--f-display);">seeker</em></h2>
            <p class="lead">Begin free, and choose an elevated tier when you desire deeper filters, contact reveals, and dedicated discovery tools.</p>
        </div>
        <div class="pkg-grid-modern">
            <?php foreach ($homeOrdered as $p):
                $isFree = ((float)$p['price']) <= 0;
                $classes = 'pkg-modern';
                if (!empty($p['ribbon'])) $classes .= ' has-ribbon';
                if (!empty($p['highlighted'])) $classes .= ' is-featured';
            ?>
                <div class="<?= $classes ?>">
                    <?php if (!empty($p['ribbon'])): ?><div class="pkg-ribbon"><?= e($p['ribbon']) ?></div><?php endif; ?>
                    <?php if (!empty($p['savings_badge'])): ?><div class="pkg-savings"><?= e($p['savings_badge']) ?></div><?php endif; ?>

                    <div class="pkg-name-modern"><?= e($p['name']) ?></div>
                    <?php if (!empty($p['tagline'])): ?><div class="pkg-tag-modern"><?= e($p['tagline']) ?></div><?php endif; ?>

                    <div class="pkg-price-modern">
                        <?php if ($isFree): ?>
                            <span class="pkg-price-value">₹0</span>
                            <span class="pkg-price-note">Lifetime Free Access</span>
                        <?php else: ?>
                            <span class="pkg-price-value"><small>₹</small><?= number_format((float)$p['price'], 0) ?></span>
                            <span class="pkg-price-note">
                                <?= (int)$p['duration_months'] ?> month<?= ((int)$p['duration_months']) === 1 ? '' : 's' ?>
                                <?php if (!empty($p['monthly_display']) && (int)$p['duration_months'] > 1): ?>
                                    · ₹<?= number_format((float)$p['monthly_display'], 0) ?>/mo
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <ul class="pkg-features-modern">
                        <?php foreach (explode("\n", (string)($p['features'] ?? '')) as $f):
                            $f = trim($f); if (!$f) continue; ?>
                            <li><?= e($f) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="pkg-cta-modern">
                        <?php if ($isFree): ?>
                            <a href="<?= Auth::check() ? '/dashboard' : '/register' ?>" class="btn btn-ghost btn-block">Start Free</a>
                        <?php else: ?>
                            <a href="/packages" class="btn <?= !empty($p['highlighted']) ? 'btn-gold' : 'btn-primary' ?> btn-block">
                                Choose <?= e($p['name']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/packages" class="btn btn-ghost">Compare All Membership Benefits →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- BLOG -->
<?php if ($posts): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Wisdom &amp; Guidance</span>
            <h2>From our <em style="color: var(--c-saffron); font-family: var(--f-display);">satsang</em></h2>
            <p class="lead">Quiet contemplation on dharma, sadhana, relationships, and conscious partnership.</p>
        </div>
        <div class="blog-grid">
            <?php foreach ($posts as $p): ?>
                <article class="blog-card">
                    <div class="blog-cover">
                        <?php if ($p['cover_image']): ?>
                            <img src="<?= e(upload_url($p['cover_image'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <span style="opacity:.65;">ॐ</span>
                        <?php endif; ?>
                    </div>
                    <div class="blog-body">
                        <span class="blog-cat"><?= e($p['category']) ?></span>
                        <h3><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
                        <p class="blog-excerpt"><?= e($p['excerpt']) ?></p>
                        <div class="blog-meta">
                            <span><?= e($p['author_name']) ?></span>
                            <span>· <?= date('M j, Y', strtotime($p['published_at'] ?? $p['created_at'])) ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA SANCTUARY -->
<section class="section-tight" style="background: linear-gradient(135deg, rgba(255, 253, 247, 0.95), rgba(247, 214, 220, 0.35) 50%, rgba(239, 232, 246, 0.35) 100%); text-align: center; padding: 5rem 0;">
    <div class="container-sm">
        <div class="deco-divider"><span class="om">ॐ</span></div>
        <h2>Two souls. One path. <em style="color: var(--c-saffron); font-family: var(--f-display);">A lifetime.</em></h2>
        <p style="font-size: 1.15rem; margin: 1.2rem auto 2.2rem; max-width: 600px; color: var(--c-ink-soft);">
            Whether you walk the way of bhakti, jnana, karma, or raja yoga — there is a soul walking it alongside you. Begin your journey with intention and grace.
        </p>
        <a href="/register" class="btn btn-primary btn-lg">Begin Your Journey · Free</a>
    </div>
</section>
