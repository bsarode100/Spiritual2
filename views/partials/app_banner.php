<?php
/**
 * Smart App Banner for Google Play Store
 * Appears as a top notification bar with close (x) button.
 * Remembers user dismissal via localStorage (7-day cooldown).
 * Automatically suppressed inside the native Android app across all versions.
 */
$ua  = $_SERVER['HTTP_USER_AGENT'] ?? '';
$xrw = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
$isNativeApp = !empty($_COOKIE['is_native_app'])
    || !empty($_SERVER['HTTP_X_SPIRITUALSHAADI_APP'])
    || str_contains($ua, 'SpiritualShaadiApp')
    || str_contains($xrw, 'com.spiritualshaadi')
    || str_contains($xrw, 'spiritualshaadi')
    || (str_contains($ua, 'Android') && str_contains($ua, 'Version/') && str_contains($ua, 'Chrome/'))
    || (str_contains($ua, 'Android') && str_contains($ua, '; wv'));

if ($isNativeApp) {
    return; // Completely suppress banner on server side for Android app
}

$playStoreUrl = 'https://play.google.com/store/apps/details?id=com.spiritualshaadi&pcampaignid=web_share';
?>
<div id="ss-app-banner" class="ss-app-banner" style="display:none;" role="banner" aria-label="SpiritualShaadi Android App">
    <div class="ss-banner-inner">
        <button type="button" id="ss-banner-close" class="ss-banner-close" aria-label="Close banner" title="Dismiss">&times;</button>
        <a href="<?= e($playStoreUrl) ?>" target="_blank" rel="noopener" class="ss-banner-link">
            <div class="ss-banner-icon">
                <img src="<?= asset('images/logo.png') ?>" alt="SpiritualShaadi App Icon">
            </div>
            <div class="ss-banner-content">
                <div class="ss-banner-title">
                    <strong>Spiritual Shaadi</strong>
                    <span class="ss-banner-verified-badge" title="Official Matrimony App">✓</span>
                </div>
                <div class="ss-banner-subtitle">
                    <span class="ss-banner-stars">★★★★★</span>
                    <span class="ss-banner-meta">4.8 • Free on Google Play</span>
                </div>
            </div>
        </a>
        <a href="<?= e($playStoreUrl) ?>" target="_blank" rel="noopener" class="ss-banner-cta">
            <svg class="ss-banner-play-icon" viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                <path d="M3.609 1.814L13.793 12 3.61 22.186c-.378-.344-.61-.836-.61-1.396V3.21c0-.56.232-1.052.609-1.396zm11.305 11.305l2.093 2.093-11.23 6.452 9.137-8.545zm2.793-1.699c.394.227.693.634.693 1.116s-.299.889-.693 1.116l-2.093 1.208-2.222-2.222 2.222-2.222 2.093 1.004zm-2.793-1.699L5.777 1.176l11.23 6.452-2.093 2.093z"/>
            </svg>
            <span>INSTALL</span>
        </a>
    </div>
</div>

<style>
.ss-app-banner {
    position: relative;
    z-index: 1000;
    background: #FAF7F2;
    border-bottom: 1px solid #E6DEC9;
    box-shadow: 0 2px 8px rgba(44, 30, 20, 0.06);
    font-family: var(--f-sans, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif);
    transition: transform 0.25s ease, opacity 0.25s ease, max-height 0.25s ease;
    overflow: hidden;
}
.ss-banner-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ss-banner-close {
    background: none;
    border: none;
    font-size: 22px;
    line-height: 1;
    color: #8C7B6D;
    cursor: pointer;
    padding: 6px 8px;
    margin: -6px 0 -6px -4px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s, background 0.15s;
    flex-shrink: 0;
}
.ss-banner-close:hover,
.ss-banner-close:focus {
    color: #2C1E14;
    background: rgba(0, 0, 0, 0.05);
    outline: none;
}
.ss-banner-link {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
    text-decoration: none;
    color: inherit;
}
.ss-banner-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    background: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ss-banner-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.ss-banner-content {
    min-width: 0;
    flex: 1;
}
.ss-banner-title {
    font-size: 13.5px;
    color: #2C1E14;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    align-items: center;
    gap: 4px;
}
.ss-banner-verified-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 13px;
    height: 13px;
    background: #01875F;
    color: #FFF;
    border-radius: 50%;
    font-size: 9px;
    font-weight: 700;
}
.ss-banner-subtitle {
    font-size: 11px;
    color: #7A6958;
    line-height: 1.25;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    align-items: center;
    gap: 5px;
}
.ss-banner-stars {
    color: #F59E0B;
    letter-spacing: 0.5px;
    font-size: 10px;
}
.ss-banner-cta {
    background: linear-gradient(135deg, #01875F 0%, #006947 100%);
    color: #FFFFFF !important;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 7px 14px;
    border-radius: 20px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 6px rgba(1, 135, 95, 0.28);
    transition: transform 0.15s, box-shadow 0.15s, background 0.15s;
    flex-shrink: 0;
}
.ss-banner-cta:hover {
    background: linear-gradient(135deg, #01996C 0%, #017852 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(1, 135, 95, 0.35);
}
@media (max-width: 480px) {
    .ss-banner-inner {
        padding: 6px 10px;
        gap: 8px;
    }
    .ss-banner-icon {
        width: 34px;
        height: 34px;
    }
    .ss-banner-title {
        font-size: 12.5px;
    }
    .ss-banner-subtitle {
        font-size: 10.5px;
    }
    .ss-banner-cta {
        padding: 5px 11px;
        font-size: 11px;
    }
}
</style>

<script>
(function() {
    try {
        var banner = document.getElementById('ss-app-banner');
        if (!banner) return;

        // 1. Comprehensive Native App / WebView Detection
        var isNativeApp = false;
        if (window.SpiritualShaadiNative || window.isSpiritualShaadiApp) {
            isNativeApp = true;
        }
        if (document.cookie && document.cookie.indexOf('is_native_app=1') !== -1) {
            isNativeApp = true;
        }
        var ua = (navigator.userAgent || navigator.vendor || window.opera || '').toLowerCase();
        if (ua.indexOf('spiritualshaadiapp') !== -1 ||
            ua.indexOf('com.spiritualshaadi') !== -1 ||
            (ua.indexOf('android') !== -1 && ua.indexOf('wv') !== -1) ||
            (ua.indexOf('android') !== -1 && ua.indexOf('version/') !== -1 && ua.indexOf('chrome/') !== -1)) {
            isNativeApp = true;
        }
        if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
            isNativeApp = true;
        }

        if (isNativeApp) {
            banner.remove();
            return;
        }

        // 2. Check 7-day dismissal cooldown
        var dismissedTime = localStorage.getItem('ss_play_banner_dismissed');
        if (dismissedTime) {
            var diffDays = (Date.now() - parseInt(dismissedTime, 10)) / (1000 * 60 * 60 * 24);
            if (diffDays < 7) {
                banner.remove();
                return;
            }
        }

        // Show banner only to non-app web visitors
        banner.style.display = 'block';

        // 3. Handle Close Click
        var closeBtn = document.getElementById('ss-banner-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                try {
                    localStorage.setItem('ss_play_banner_dismissed', Date.now().toString());
                } catch(err) {}
                banner.style.opacity = '0';
                banner.style.maxHeight = '0';
                banner.style.transform = 'translateY(-100%)';
                setTimeout(function() {
                    banner.remove();
                }, 260);
            });
        }
    } catch(err) {
        // Safe fallback
    }
})();
</script>
