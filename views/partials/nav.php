<nav class="nav">
    <div class="container nav-inner">
        <a href="/" class="brand">
            <div class="brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><text x="12" y="18" text-anchor="middle" font-family="serif" font-size="20" fill="currentColor">ॐ</text></svg>
            </div>
            <span><?= e(setting('site_name', 'Spiritual Matrimony')) ?></span>
        </a>

        <ul class="nav-links" style="list-style: none; padding: 0; margin: 0;">
            <li><a href="/" class="<?= nav_active('/') ?>">Home</a></li>
            <li><a href="/about" class="<?= nav_active('/about') ?>">About</a></li>
            <li><a href="/browse" class="<?= nav_active('/browse') ?>">Browse</a></li>
            <li><a href="/packages" class="<?= nav_active('/packages') ?>">Packages</a></li>
            <li><a href="/happy-stories" class="<?= nav_active('/happy-stories') ?>">Happy Stories</a></li>
            <li><a href="/blog" class="<?= nav_active('/blog') ?>">Blog</a></li>
            <li><a href="/contact" class="<?= nav_active('/contact') ?>">Contact</a></li>
        </ul>

        <div class="nav-cta">
            <?php if (Auth::check()): ?>
                <?php if (!Auth::isAdmin()): $navBadge = membership_badge(Auth::id()); ?>
                    <?php if ($navBadge): ?>
                        <a href="/packages" class="nav-badge" title="<?= e($navBadge) ?> member"><?= e($navBadge) ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="<?= Auth::isAdmin() ? '/admin' : '/dashboard' ?>" class="btn btn-ghost btn-sm"><?= Auth::isAdmin() ? 'Admin' : 'Dashboard' ?></a>
                <a href="/logout" class="btn btn-primary btn-sm">Sign Out</a>
            <?php else: ?>
                <a href="/login" class="btn btn-ghost btn-sm">Sign In</a>
                <a href="/register" class="btn btn-primary btn-sm">Join Free</a>
            <?php endif; ?>
            <button class="mobile-toggle" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
</nav>
