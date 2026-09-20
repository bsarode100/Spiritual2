<?php
// Mobile-only bottom navigation bar (strictly hidden on desktop / laptop screens via CSS)
// Shaadi.com / Jeevansathi-inspired 5-tab layout: Home · Matches · Activity · Messages · Upgrade
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
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <span>Matches</span>
    </a>

    <?php if ($isAuth): ?>
        <a href="/interests" class="bottom-nav-item <?= str_starts_with($currentUri, '/interests') || str_starts_with($currentUri, '/shortlist') || str_starts_with($currentUri, '/visitors') || str_starts_with($currentUri, '/shortlisted-by') ? 'active' : '' ?>">
            <div class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </div>
            <span>Activity</span>
        </a>

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

        <a href="/packages" class="bottom-nav-item bottom-nav-upgrade <?= str_starts_with($currentUri, '/packages') || str_starts_with($currentUri, '/billing') ? 'active' : '' ?>">
            <div class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </div>
            <span>Upgrade</span>
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
