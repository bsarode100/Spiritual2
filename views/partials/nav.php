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
                <li class="mobile-nav-user">
                    <div class="mobile-user-greeting">
                        <div class="mobile-user-avatar"><?= mb_strtoupper(mb_substr(Auth::user()['name'] ?? 'S', 0, 1)) ?></div>
                        <div class="mobile-user-info">
                            <strong><?= e(Auth::user()['name'] ?? 'Seeker') ?></strong>
                            <span><?= e(Auth::user()['email'] ?? '') ?></span>
                        </div>
                    </div>
                </li>
                <li class="mobile-nav-item"><a href="<?= Auth::isAdmin() ? '/admin' : '/dashboard' ?>" class="<?= nav_active('/dashboard') ?>">Dashboard</a></li>
                <li class="mobile-nav-item"><a href="/profile" class="<?= nav_active('/profile') ?>">My Profile</a></li>
                <li class="mobile-nav-item"><a href="/messages" class="<?= nav_active('/messages') ?>">Messages</a></li>
                <li class="mobile-nav-divider"></li>
            <?php endif; ?>

            <li><a href="/" class="<?= nav_active('/') ?>">Home</a></li>
            <li><a href="/about" class="<?= nav_active('/about') ?>">About</a></li>
            <li><a href="/browse" class="<?= nav_active('/browse') ?>">Browse</a></li>
            <li><a href="/packages" class="<?= nav_active('/packages') ?>">Packages</a></li>
            <li><a href="/happy-stories" class="<?= nav_active('/happy-stories') ?>">Happy Stories</a></li>
            <li><a href="/blog" class="<?= nav_active('/blog') ?>">Blog</a></li>
            <li><a href="/contact" class="<?= nav_active('/contact') ?>">Contact</a></li>

            <?php if (Auth::check()): ?>
                <li class="mobile-nav-divider"></li>
                <li class="mobile-nav-item"><a href="/logout" class="mobile-signout-link">Sign Out</a></li>
            <?php else: ?>
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
                <a href="<?= Auth::isAdmin() ? '/admin' : '/dashboard' ?>" class="btn btn-primary btn-sm nav-mobile-dash" title="Dashboard">Dashboard</a>
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
