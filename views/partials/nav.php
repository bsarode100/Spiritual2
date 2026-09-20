<nav class="nav">
    <div class="container-lg nav-inner">
        <a href="/" class="brand">
            <div class="brand-icon">
                <img src="<?= asset('images/logo.png') ?>" alt="SpiritualShaadi Logo" style="height: 36px; width: auto;">
            </div>
            <div class="brand-text">
                <span><?= e(setting('site_name', 'SpiritualShaadi')) ?></span>
                <small class="brand-motto">Find a Perfect Spiritual Life Partner</small>
            </div>
        </a>

        <ul class="nav-links">
            <?php if (Auth::check()): ?>
                <!-- Mobile User Profile Card -->
                <li class="mobile-nav-user">
                    <div class="mobile-user-greeting">
                        <div class="mobile-user-avatar"><?= mb_strtoupper(mb_substr(Auth::user()['name'] ?? 'S', 0, 1)) ?></div>
                        <div class="mobile-user-info">
                            <strong><?= e(Auth::user()['name'] ?? 'Seeker') ?></strong>
                            <span><?= e(Auth::user()['email'] ?? '') ?></span>
                            <?php if (!Auth::isAdmin() && ($uBadge = membership_badge(Auth::id()))): ?>
                                <small class="mobile-user-plan">★ <?= e($uBadge) ?> Member</small>
                            <?php endif; ?>
                        </div>
                        <a href="/profile/edit" class="mobile-edit-btn" title="Edit Profile">✏️</a>
                    </div>
                </li>

                <!-- CATEGORY 1: Profile & Verification -->
                <li class="mobile-nav-section-title"><span>👤 Profile &amp; Verification</span></li>
                <li class="mobile-nav-item"><a href="/profile/edit"><span class="nav-icon">✏️</span> Edit Profile</a></li>
                <li class="mobile-nav-item"><a href="/profile/photos"><span class="nav-icon">📸</span> Manage Photos</a></li>
                <li class="mobile-nav-item"><a href="/verification"><span class="nav-icon">🛡️</span> Get Verified <span class="nav-sub-badge">Trust Badge</span></a></li>

                <!-- CATEGORY 2: Matches & Activity -->
                <li class="mobile-nav-section-title"><span>💖 Matches &amp; Activity</span></li>
                <li class="mobile-nav-item"><a href="<?= Auth::isAdmin() ? '/admin' : '/dashboard' ?>" class="<?= nav_active('/dashboard') ?>"><span class="nav-icon">🏠</span> Dashboard</a></li>
                <li class="mobile-nav-item"><a href="/browse" class="<?= nav_active('/browse') ?>"><span class="nav-icon">🔍</span> Browse Seekers</a></li>
                <li class="mobile-nav-item"><a href="/interests" class="<?= nav_active('/interests') ?>"><span class="nav-icon">💌</span> Interests</a></li>
                <li class="mobile-nav-item"><a href="/shortlist" class="<?= nav_active('/shortlist') ?>"><span class="nav-icon">⭐</span> My Shortlist</a></li>
                <li class="mobile-nav-item"><a href="/visitors" class="<?= nav_active('/visitors') ?>"><span class="nav-icon">👁️</span> Profile Visitors</a></li>
                <li class="mobile-nav-item"><a href="/shortlisted-by" class="<?= nav_active('/shortlisted-by') ?>"><span class="nav-icon">👥</span> Shortlisted Me</a></li>
                <li class="mobile-nav-item"><a href="/messages" class="<?= nav_active('/messages') ?>"><span class="nav-icon">💬</span> Messages</a></li>

                <!-- CATEGORY 3: Membership & Billing -->
                <li class="mobile-nav-section-title"><span>💎 Membership &amp; Billing</span></li>
                <li class="mobile-nav-item"><a href="/packages" class="<?= nav_active('/packages') ?>"><span class="nav-icon">💎</span> Membership Plans</a></li>
                <li class="mobile-nav-item"><a href="/billing" class="<?= nav_active('/billing') ?>"><span class="nav-icon">💳</span> Billing &amp; Receipts</a></li>

                <!-- CATEGORY 4: Account & Explore -->
                <li class="mobile-nav-section-title"><span>⚙️ Account &amp; Explore</span></li>
                <li class="mobile-nav-item"><a href="/settings" class="<?= nav_active('/settings') ?>"><span class="nav-icon">⚙️</span> Account Settings</a></li>
                <li class="mobile-nav-item"><a href="/happy-stories" class="<?= nav_active('/happy-stories') ?>"><span class="nav-icon">🪷</span> Happy Stories</a></li>
                <li class="mobile-nav-item"><a href="/blog" class="<?= nav_active('/blog') ?>"><span class="nav-icon">📖</span> Spiritual Wisdom</a></li>
                <li class="mobile-nav-item"><a href="/contact" class="<?= nav_active('/contact') ?>"><span class="nav-icon">📞</span> Contact Support</a></li>
                <li class="mobile-nav-divider"></li>
                <li class="mobile-nav-item"><a href="/logout" class="mobile-signout-link"><span class="nav-icon">🚪</span> Sign Out</a></li>
            <?php endif; ?>

            <!-- Desktop-Only Standard Navigation Items -->
            <li class="nav-desktop-item"><a href="/" class="<?= nav_active('/') ?>">Home</a></li>
            <li class="nav-desktop-item"><a href="/about" class="<?= nav_active('/about') ?>">About</a></li>
            <li class="nav-desktop-item"><a href="/browse" class="<?= nav_active('/browse') ?>">Browse</a></li>
            <li class="nav-desktop-item"><a href="/packages" class="<?= nav_active('/packages') ?>">Packages</a></li>
            <li class="nav-desktop-item"><a href="/happy-stories" class="<?= nav_active('/happy-stories') ?>">Happy Stories</a></li>
            <li class="nav-desktop-item"><a href="/blog" class="<?= nav_active('/blog') ?>">Blog</a></li>
            <li class="nav-desktop-item"><a href="/contact" class="<?= nav_active('/contact') ?>">Contact</a></li>

            <?php if (!Auth::check()): ?>
                <li class="mobile-nav-divider"></li>
                <li class="mobile-nav-auth-buttons">
                    <a href="/login" class="btn btn-ghost btn-sm" style="flex: 1; justify-content: center;">Sign In</a>
                    <a href="/register" class="btn btn-primary btn-sm" style="flex: 1; justify-content: center;">Join Free</a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="nav-cta">
            <?php if (Auth::check()): ?>
                <?php if (!Auth::isAdmin()): $navBadge = membership_badge(Auth::id()); ?>
                    <?php if ($navBadge): ?>
                        <a href="/packages" class="nav-badge" title="<?= e($navBadge) ?> member"><?= e($navBadge) ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="<?= Auth::isAdmin() ? '/admin' : '/dashboard' ?>" class="btn btn-ghost btn-sm nav-desktop-link"><?= Auth::isAdmin() ? 'Admin' : 'Dashboard' ?></a>
                <a href="/logout" class="btn btn-primary btn-sm nav-desktop-link">Sign Out</a>
            <?php else: ?>
                <a href="/login" class="btn btn-ghost btn-sm nav-desktop-link">Sign In</a>
                <a href="/register" class="btn btn-primary btn-sm">Join Free</a>
            <?php endif; ?>
            <button class="mobile-toggle" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
</nav>
