<?php /** @var array $featured, $stories, $packages, $posts */ ?>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">ॐ &middot; Sincere Seekers, United</span>
                <h1>
                    <?= e(setting('hero_heading','Find a partner who walks your')) ?>
                    <span class="gold-accent">spiritual path</span>
                </h1>
                <p class="hero-sub"><?= e(setting('hero_subheading','A sacred space for sincere seekers to find a life-companion rooted in dharma, sadhana, and love.')) ?></p>
                <div class="hero-cta">
                    <a href="/register" class="btn btn-primary btn-lg">
                        <?= e(setting('hero_cta_text','Begin Your Journey')) ?>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="/browse" class="btn btn-ghost btn-lg">Browse Profiles</a>
                </div>
                <div class="hero-trust">
                    <div class="avatars">
                        <div></div><div></div><div></div><div></div>
                    </div>
                    <div><strong style="color: var(--c-ink);"><?= e(setting('stat_marriages','1,200+')) ?> sacred unions</strong> · across <?= e(setting('stat_paths','18')) ?> spiritual paths</div>
                </div>
            </div>

            <div class="hero-visual">
                <!-- Background mandala -->
                <svg class="mandala" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <g fill="none" stroke="#D4A017" stroke-width="0.4" opacity="0.6">
                        <circle cx="100" cy="100" r="95"/>
                        <circle cx="100" cy="100" r="75"/>
                        <circle cx="100" cy="100" r="55"/>
                        <circle cx="100" cy="100" r="35"/>
                        <?php for ($i = 0; $i < 12; $i++) {
                            $angle = $i * 30;
                            echo "<line x1='100' y1='100' x2='100' y2='5' transform='rotate($angle 100 100)'/>";
                        } ?>
                    </g>
                </svg>

                <!-- Card 3 (back small) -->
                <div class="hero-card hero-card-3">
                    <div>
                        <div class="script" style="color: var(--c-saffron);">सहजीवन</div>
                        <div style="font-size: .9rem; opacity: .9;">Walk together</div>
                    </div>
                </div>

                <!-- Card 2 (medium) -->
                <div class="hero-card hero-card-2">
                    <div style="height: 100%; background: linear-gradient(135deg, #E8A23B 0%, #7B1F1F 100%); display: flex; align-items: flex-end; padding: 1.4rem; color: white;">
                        <div>
                            <div style="font-size: .75rem; letter-spacing: .15em; opacity: .9; margin-bottom: .3rem;">VIPASSANA · 6 YRS</div>
                            <div style="font-family: var(--f-display); font-size: 1.4rem;">Arjun, 31</div>
                            <div style="font-size: .85rem; opacity: .8;">Engineer, Bengaluru</div>
                        </div>
                    </div>
                </div>

                <!-- Card 1 (main, front) -->
                <div class="hero-card hero-card-main">
                    <div style="height: 100%; background: linear-gradient(135deg, #FAF3E0 0%, #E8A6A6 100%); position: relative;">
                        <svg viewBox="0 0 200 240" style="width: 100%; height: 100%;" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="g1" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0" stop-color="#E8A6A6"/>
                                    <stop offset="1" stop-color="#7B1F1F"/>
                                </linearGradient>
                            </defs>
                            <rect width="200" height="240" fill="url(#g1)"/>
                            <!-- Stylized portrait shape -->
                            <circle cx="100" cy="90" r="36" fill="#FAF3E0" opacity=".85"/>
                            <path d="M40 240 Q40 140 100 140 Q160 140 160 240 Z" fill="#FAF3E0" opacity=".85"/>
                            <!-- bindi -->
                            <circle cx="100" cy="74" r="3" fill="#7B1F1F"/>
                        </svg>
                    </div>
                    <div class="hero-card-badge">
                        <h4>Anjali, 28</h4>
                        <p>Yoga teacher · Bhakti path · Pune</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div><div class="stat-num"><?= e(setting('stat_members','25,000+')) ?></div><div class="stat-label">Sincere Seekers</div></div>
            <div><div class="stat-num"><?= e(setting('stat_marriages','1,200+')) ?></div><div class="stat-label">Sacred Unions</div></div>
            <div><div class="stat-num"><?= e(setting('stat_paths','18')) ?></div><div class="stat-label">Spiritual Paths</div></div>
            <div><div class="stat-num"><?= e(setting('stat_countries','40+')) ?></div><div class="stat-label">Countries</div></div>
        </div>
    </div>
</section>

<!-- WHY US -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Why we are different</span>
            <h2>Not just bio-data. <em style="color: var(--c-saffron); font-family: var(--f-display);">Dharma-data.</em></h2>
            <p class="lead">Traditional matrimony sites ask about salary and skin tone. We ask about your guru, your sadhana, your ishta-devata. Because the most important question for a sadhak's life-partner is one no other site asks.</p>
        </div>

        <div class="features-grid">
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L4 7v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V7l-8-5z"/></svg>
                </div>
                <h3>Verified Seekers</h3>
                <p>Every profile is reviewed by our team. We are a small, sincere community — not a numbers game. No fake profiles, no inflated claims.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>
                </div>
                <h3>Spiritual Compatibility</h3>
                <p>Filter by lineage, guru, sadhana style, diet, and path. The deepest filters in any matrimony platform — because depth is what matters.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </div>
                <h3>Family-Friendly</h3>
                <p>Built for the traditional Indian family — with privacy controls, family-involved conversations, and respectful matchmaking.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                </div>
                <h3>Real Conversations</h3>
                <p>Once an interest is accepted, talk directly — no premium gates for genuine connection. Inner growth begins with truthful dialogue.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h3>Privacy-First</h3>
                <p>Your photos and contact details stay private until you choose to share them. You control your visibility — always.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>Concierge Matchmaking</h3>
                <p>Sangam premium members get a personal matchmaker — a real human, deeply familiar with our community — who handpicks introductions.</p>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED PROFILES -->
<?php if ($featured): ?>
<section class="section section-soft">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Meet our community</span>
            <h2>Recent <em style="color: var(--c-saffron); font-family: var(--f-display);">seekers</em></h2>
            <p class="lead">A few of our newest members. Sign in to see their full profiles, send an interest, and begin a conversation.</p>
        </div>
        <div class="profiles-grid">
            <?php foreach (array_slice($featured, 0, 6) as $m):
                $age = age_from_dob($m['dob']); ?>
                <article class="profile-card">
                    <div class="profile-photo">
                        <img src="<?= e(avatar_url($m)) ?>" alt="<?= e($m['name']) ?>">
                        <?php if ($m['spiritual_path']): ?>
                            <span class="profile-badge"><?= e($m['spiritual_path']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="profile-body">
                        <h3><?= e($m['name']) ?><?php if ($age): ?>, <?= $age ?><?php endif; ?></h3>
                        <div class="profile-meta">
                            <?= e($m['profession'] ?: 'Seeker') ?> · <?= e(trim($m['city'] . ' · ' . $m['state'], ' ·')) ?>
                        </div>
                        <div class="profile-tags">
                            <?php if ($m['height_cm']): ?><span class="tag"><?= cm_to_feet((int)$m['height_cm']) ?></span><?php endif; ?>
                            <?php if ($m['education']): ?><span class="tag"><?= e($m['education']) ?></span><?php endif; ?>
                        </div>
                        <p class="profile-about"><?= e($m['about_me'] ?? '') ?></p>
                        <a href="/member/<?= (int)$m['id'] ?>" class="btn btn-ghost btn-sm">View Profile</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/browse" class="btn btn-primary btn-lg">Browse All Profiles →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- HOW IT WORKS -->
<section class="section section-dark">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Your journey</span>
            <h2>Three steps to <em style="font-family: var(--f-display); color: var(--c-saffron);">grihastha dharma</em></h2>
        </div>
        <div class="features-grid">
            <div style="background: rgba(255,248,238,.06); border-radius: var(--r-lg); padding: 2.5rem 2rem; backdrop-filter: blur(6px); border: 1px solid rgba(255,248,238,.1);">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--c-saffron); color: var(--c-indigo); display: flex; align-items: center; justify-content: center; font-family: var(--f-display); font-size: 1.6rem; font-weight: 700; margin-bottom: 1.4rem;">१</div>
                <h3 style="color: var(--c-cream);">Create your sankalpa</h3>
                <p>Sign up free. Build a profile that reflects who you truly are — your sadhana, your guru, your ishta-devata, and the partner your heart seeks.</p>
            </div>
            <div style="background: rgba(255,248,238,.06); border-radius: var(--r-lg); padding: 2.5rem 2rem; backdrop-filter: blur(6px); border: 1px solid rgba(255,248,238,.1);">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--c-saffron); color: var(--c-indigo); display: flex; align-items: center; justify-content: center; font-family: var(--f-display); font-size: 1.6rem; font-weight: 700; margin-bottom: 1.4rem;">२</div>
                <h3 style="color: var(--c-cream);">Discover sangha</h3>
                <p>Browse, search, and shortlist profiles. Use our deep spiritual filters to find seekers walking your specific path.</p>
            </div>
            <div style="background: rgba(255,248,238,.06); border-radius: var(--r-lg); padding: 2.5rem 2rem; backdrop-filter: blur(6px); border: 1px solid rgba(255,248,238,.1);">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--c-saffron); color: var(--c-indigo); display: flex; align-items: center; justify-content: center; font-family: var(--f-display); font-size: 1.6rem; font-weight: 700; margin-bottom: 1.4rem;">३</div>
                <h3 style="color: var(--c-cream);">Begin your conversation</h3>
                <p>Send an interest. When accepted, message each other directly. Take it offline, meet families, and walk together.</p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="/register" class="btn btn-gold btn-lg">Begin Free Today</a>
        </div>
    </div>
</section>

<!-- HAPPY STORIES -->
<?php if ($stories): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Sacred unions</span>
            <h2>Happy <em style="color: var(--c-saffron); font-family: var(--f-display);">stories</em></h2>
            <p class="lead">Souls who found each other here, and now walk the path together.</p>
        </div>
        <div class="features-grid">
            <?php foreach (array_slice($stories, 0, 3) as $s): ?>
                <div class="story-card">
                    <p><?= e($s['story']) ?></p>
                    <div class="story-couple">
                        <div class="story-couple-avatar">💞</div>
                        <div>
                            <div class="story-couple-name"><?= e($s['couple_name']) ?></div>
                            <?php if ($s['married_on']): ?>
                                <div class="story-couple-date">Married <?= date('M Y', strtotime($s['married_on'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/happy-stories" class="btn btn-ghost">Read more stories →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- PACKAGES -->
<?php if ($packages): ?>
<section class="section section-soft">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Choose your path</span>
            <h2>Three plans. <em style="color: var(--c-saffron); font-family: var(--f-display);">No tricks.</em></h2>
            <p class="lead">Pay only if you want to. Free Sadhak access lets you experience the community fully before deciding.</p>
        </div>
        <div class="pkg-grid">
            <?php foreach ($packages as $p): ?>
                <div class="pkg <?= $p['highlighted'] ? 'featured' : '' ?>">
                    <div class="pkg-name"><?= e($p['name']) ?></div>
                    <div class="pkg-tag"><?= e($p['tagline']) ?></div>
                    <div class="pkg-price">
                        <?php if ($p['price'] > 0): ?>
                            <small>₹</small><?= number_format((float)$p['price'], 0) ?>
                        <?php else: ?>
                            Free
                        <?php endif; ?>
                    </div>
                    <div class="pkg-duration"><?= (int)$p['duration_days'] ?> days</div>
                    <ul class="pkg-features">
                        <?php foreach (explode("\n", $p['features'] ?? '') as $f):
                            $f = trim($f); if (!$f) continue; ?>
                            <li><?= e($f) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="/register" class="btn <?= $p['highlighted'] ? 'btn-gold' : 'btn-ghost' ?> btn-block">
                        <?= $p['price'] > 0 ? 'Choose ' . e($p['name']) : 'Start Free' ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- BLOG -->
<?php if ($posts): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Wisdom &amp; guidance</span>
            <h2>From our <em style="color: var(--c-saffron); font-family: var(--f-display);">satsang</em></h2>
            <p class="lead">Writing on dharma, household life, sadhana, and partnership — for sincere seekers.</p>
        </div>
        <div class="blog-grid">
            <?php foreach ($posts as $p): ?>
                <article class="blog-card">
                    <div class="blog-cover">
                        <?php if ($p['cover_image']): ?>
                            <img src="<?= e(upload_url($p['cover_image'])) ?>" alt="<?= e($p['title']) ?>">
                        <?php else: ?>
                            <span style="opacity:.7;">ॐ</span>
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

<!-- CTA -->
<section class="section-tight" style="background: linear-gradient(135deg, var(--c-cream-2), var(--c-sand)); text-align: center; padding-bottom: 5rem; padding-top: 4rem;">
    <div class="container-sm">
        <div class="deco-divider"><span class="om">ॐ</span></div>
        <h2>Two souls. One path. <em style="color: var(--c-saffron); font-family: var(--f-display);">A lifetime.</em></h2>
        <p style="font-size: 1.15rem; margin: 1.5rem 0 2.5rem;">Whether you walk the way of bhakti, jnana, karma, or raja — there is a soul out there walking it too. Let us help you find each other.</p>
        <a href="/register" class="btn btn-primary btn-lg">Begin Your Journey · Free</a>
    </div>
</section>
