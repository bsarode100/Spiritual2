<?php
// Mobile-only bottom navigation bar (strictly hidden on desktop / laptop screens via CSS)
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isAuth = Auth::check();
$unreadCount = $isAuth ? unread_messages_count(Auth::id()) : 0;
?>
<nav class="mobile-bottom-nav" aria-label="Mobile Navigation">
    <a href="/" class="bottom-nav-item <?= $currentUri === '/' ? 'active' : '' ?>">
        <div class="bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>
        <span>Home</span>
    </a>

    <a href="/browse" class="bottom-nav-item <?= str_starts_with($currentUri, '/browse') ? 'active' : '' ?>">
        <div class="bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
        </div>
        <span>Browse</span>
    </a>

    <?php if ($isAuth): ?>
        <a href="/messages" class="bottom-nav-item <?= str_starts_with($currentUri, '/messages') ? 'active' : '' ?>">
            <div class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
                <?php if ($unreadCount > 0): ?>
                    <span class="bottom-nav-badge"><?= $unreadCount > 9 ? '9+' : $unreadCount ?></span>
                <?php endif; ?>
            </div>
            <span>Messages</span>
        </a>

        <a href="<?= Auth::isAdmin() ? '/admin' : '/dashboard' ?>" class="bottom-nav-item <?= str_starts_with($currentUri, '/dashboard') || str_starts_with($currentUri, '/profile') || str_starts_with($currentUri, '/admin') ? 'active' : '' ?>">
            <div class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <span><?= Auth::isAdmin() ? 'Admin' : 'Dashboard' ?></span>
        </a>
    <?php else: ?>
        <a href="/login" class="bottom-nav-item <?= $currentUri === '/login' ? 'active' : '' ?>">
            <div class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
            </div>
            <span>Sign In</span>
        </a>

        <a href="/register" class="bottom-nav-item bottom-nav-join <?= $currentUri === '/register' ? 'active' : '' ?>">
            <div class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
            </div>
            <span>Join Free</span>
        </a>
    <?php endif; ?>
</nav>
