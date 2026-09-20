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
                <input type="text" name="path" list="home_path_list" placeholder="e.g. Advaita Vedanta, Bhakti Yoga..." class="glass-control">
                <datalist id="home_path_list">
                    <?php foreach (spiritual_paths() as $hp): ?>
                        <option value="<?= e($hp) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
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
                <input type="text" name="city" placeholder="Any City / State" class="glass-control">
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

    <!-- 4-Column Feature Highlights (Reference Design Kit Row) -->
    <div class="hero-features-row">
        <div class="hero-feat-card">
            <div class="hero-feat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            </div>
            <div class="hero-feat-content">
                <h4>Verified Profiles</h4>
                <p>100% human-verified seekers with optional ID &amp; selfie checks.</p>
            </div>
        </div>
        <div class="hero-feat-card">
            <div class="hero-feat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>
            </div>
            <div class="hero-feat-content">
                <h4>Spiritual Values</h4>
                <p>Filter by lineage, sadhana, guru, daily practice, and diet.</p>
            </div>
        </div>
        <div class="hero-feat-card">
            <div class="hero-feat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <div class="hero-feat-content">
                <h4>Genuine Intentions</h4>
                <p>Sincere seekers dedicated to sacred grihastha dharma.</p>
            </div>
        </div>
        <div class="hero-feat-card">
            <div class="hero-feat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div class="hero-feat-content">
                <h4>Safe &amp; Private</h4>
                <p>Discreet contact and full granular control over photo privacy.</p>
            </div>
        </div>
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

<!-- FIND SOMEONE WHO TRULY ALIGNS (EDITORIAL DESIGN REFERENCE SECTION) -->
<section class="align-section">
    <div class="container">
        <div class="align-grid">
            <div class="align-visual-wrapper">
                <div class="align-visual-card">
                    <svg viewBox="0 0 520 400" style="width: 100%; height: auto; display: block;" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="auroraSky" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#FFF8F2"/>
                                <stop offset="45%" stop-color="#FCEBEF"/>
                                <stop offset="85%" stop-color="#EEDDF8"/>
                                <stop offset="100%" stop-color="#E5D6F5"/>
                            </linearGradient>
                            <radialGradient id="sunGlow" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#F6C177" stop-opacity="0.85"/>
                                <stop offset="60%" stop-color="#F6C177" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#F6C177" stop-opacity="0"/>
                            </radialGradient>
                            <linearGradient id="coupleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#482E63"/>
                                <stop offset="100%" stop-color="#2D1942"/>
                            </linearGradient>
                        </defs>
                        <!-- Gentle Himalayan Sunrise Sky -->
                        <rect width="520" height="400" rx="28" fill="url(#auroraSky)"/>
                        
                        <!-- Golden Dawn Sun Aura -->
                        <circle cx="260" cy="180" r="140" fill="url(#sunGlow)"/>
                        <circle cx="260" cy="180" r="60" fill="#FFF4E0" opacity="0.6"/>
                        
                        <!-- Sacred Geometry Aura Rings -->
                        <circle cx="260" cy="180" r="90" fill="none" stroke="#F6C177" stroke-width="1" stroke-dasharray="4 4" opacity="0.5"/>
                        <circle cx="260" cy="180" r="120" fill="none" stroke="#D45B7A" stroke-width="0.75" opacity="0.35"/>
                        
                        <!-- Sacred OM in the Dawn Sun -->
                        <text x="260" y="195" font-family="'Playfair Display', serif" font-size="48" fill="#BA385C" text-anchor="middle" font-weight="600" opacity="0.85">ॐ</text>
                        
                        <!-- Gentle Mountain Horizons -->
                        <path d="M0 320 Q 130 250 260 290 T 520 270 L 520 400 L 0 400 Z" fill="#EAD5EE" opacity="0.55"/>
                        <path d="M0 340 Q 160 300 300 330 T 520 310 L 520 400 L 0 400 Z" fill="#E3C4DF" opacity="0.5"/>
                        
                        <!-- Serene Meditating Couple Silhouettes (Groom & Bride in Dhyana Mudra) -->
                        <!-- Groom Sadhak (Left) -->
                        <g fill="url(#coupleGrad)" opacity="0.92">
                            <circle cx="205" cy="275" r="19"/>
                            <path d="M175 370 C175 320 188 300 205 300 C222 300 235 320 235 370 Z"/>
                            <ellipse cx="205" cy="375" rx="42" ry="16"/>
                        </g>
                        
                        <!-- Bride Sadhika (Right) -->
                        <g fill="url(#coupleGrad)" opacity="0.92">
                            <circle cx="315" cy="278" r="18"/>
                            <path d="M285 370 C285 320 298 302 315 302 C332 302 345 320 345 370 Z"/>
                            <ellipse cx="315" cy="375" rx="40" ry="15"/>
                        </g>
                        
                        <!-- Floating Lotus Petal Motifs -->
                        <path d="M260 345 C252 355 242 362 260 375 C278 362 268 355 260 345 Z" fill="#D45B7A" opacity="0.75"/>
                        <circle cx="260" cy="358" r="3" fill="#F6C177"/>
                    </svg>
                </div>
                <!-- Floating Testimonial/Badge from Reference -->
                <div class="align-floating-badge">
                    <p>“Finding someone who meditates each sunrise transformed my life.”</p>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-rose);"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        Ananya &amp; Rohit · Married 2024
                    </span>
                </div>
            </div>

            <div class="align-content">
                <span class="eyebrow" style="color: var(--c-rose);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><path d="M12 3v18M3 12h18M6 6l12 12M6 18L18 6"/></svg>
                    Sacred Harmony · Sincere Connection
                </span>
                <h2 style="font-size: clamp(2rem, 3.8vw, 3.2rem); margin: 0.4rem 0 1rem;">
                    Find Someone Who <em style="font-family: var(--f-display); color: var(--c-rose);">Truly Aligns</em>
                </h2>
                <p class="lead" style="font-size: 1.1rem; color: var(--c-ink-soft); line-height: 1.7; margin-bottom: 1.5rem;">
                    Connect with individuals who honor your spiritual philosophy, daily practices, and heartfelt commitment to conscious matrimonial partnership.
                </p>

                <ul class="align-checklist">
                    <li class="align-check-item">
                        <div class="align-check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <div class="align-check-title">Shared Sadhana &amp; Daily Practice</div>
                            <div class="align-check-desc">Find a companion who honors morning meditation, japa, and conscious stillness.</div>
                        </div>
                    </li>
                    <li class="align-check-item">
                        <div class="align-check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <div class="align-check-title">Dietary &amp; Conscious Living Harmony</div>
                            <div class="align-check-desc">Sattvic, vegetarian, vegan, and mindful living without friction or compromise.</div>
                        </div>
                    </li>
                    <li class="align-check-item">
                        <div class="align-check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <div class="align-check-title">Reverence for Dharma &amp; Sacred Union</div>
                            <div class="align-check-desc">Marriage viewed as a sacred sadhana for mutual elevation and family grace.</div>
                        </div>
                    </li>
                </ul>

                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <a href="/browse" class="btn btn-primary btn-lg">
                        Explore Compatible Seekers
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="/register" class="btn btn-ghost btn-lg">Join Free Today</a>
                </div>
            </div>
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
                            </div>
                            <div class="photo-sub">
                                <?= e($m['profession'] ?: 'Seeker') ?> · <?= e(trim($m['city'] . ' · ' . $m['state'], ' ·')) ?>
                            </div>
                        </div>
                    </div>

                    <div class="profile-body">
                        <!-- Spiritual & Lifestyle Highlights -->
                        <div class="profile-tags">
                            <?php if (!empty($m['diet'])): ?><span class="tag tag-gold">🌿 <?= ucfirst(e($m['diet'])) ?></span><?php endif; ?>
                            <?php if (!empty($m['height_cm'])): ?><span class="tag">📏 <?= cm_to_feet((int)$m['height_cm']) ?></span><?php endif; ?>
                            <?php if (!empty($m['education'])): ?><span class="tag"><?= e($m['education']) ?></span><?php endif; ?>
                        </div>

                        <?php if (!empty($m['about_me'])): ?>
                            <p class="profile-about"><?= e($m['about_me']) ?></p>
                        <?php endif; ?>

                        <div class="profile-card-actions">
                            <a href="/member/<?= (int)$m['id'] ?>" class="btn btn-ghost btn-sm" style="flex: 1;">View Profile</a>
                            <form method="post" action="/interest/send/<?= (int)$m['id'] ?>" style="margin:0;">
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
                            <a href="/packages" class="btn <?= !empty($p['highlighted']) ? 'btn-primary' : 'btn-ghost' ?> btn-block">
                                <?= e($p['name']) ?>
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
