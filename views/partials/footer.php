<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3 style="font-family: var(--f-display); font-size: 1.6rem;">
                    <span style="color: var(--c-saffron);">ॐ</span> <?= e(setting('site_name', 'Spiritual Matrimony')) ?>
                </h3>
                <p><?= e(setting('footer_about', 'Two souls. One path. A lifetime of sadhana — together.')) ?></p>
                <div class="social">
                    <?php if ($u = setting('social_facebook')): ?>
                    <a href="<?= e($u) ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if ($u = setting('social_instagram')): ?>
                    <a href="<?= e($u) ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.26.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38a3.7 3.7 0 0 1 1.38-.9c.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16zM12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63a5.86 5.86 0 0 0-2.13 1.38A5.86 5.86 0 0 0 .63 4.14C.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.8.73 1.48 1.38 2.13a5.86 5.86 0 0 0 2.13 1.38c.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.86 5.86 0 0 0 2.13-1.38 5.86 5.86 0 0 0 1.38-2.13c.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91a5.86 5.86 0 0 0-1.38-2.13A5.86 5.86 0 0 0 19.86.63C19.1.33 18.22.13 16.95.07 15.67.01 15.26 0 12 0zm0 5.84A6.16 6.16 0 1 0 12 18.16 6.16 6.16 0 0 0 12 5.84zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.85a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if ($u = setting('social_youtube')): ?>
                    <a href="<?= e($u) ?>" target="_blank" rel="noopener" aria-label="YouTube">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1c.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.6 15.6V8.4l6.2 3.6-6.2 3.6z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="/browse">Browse Profiles</a></li>
                    <li><a href="/packages">Packages</a></li>
                    <li><a href="/happy-stories">Happy Stories</a></li>
                    <li><a href="/blog">Spiritual Wisdom</a></li>
                </ul>
            </div>
            <div>
                <h4>About</h4>
                <ul>
                    <li><a href="/about">Our Story</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/payment-details">Payment Details</a></li>
                    <li><a href="/page/privacy-policy">Privacy Policy</a></li>
                    <li><a href="/page/terms-and-condition">Terms &amp; Conditions</a></li>
                    <li><a href="/refund-policy">Refund &amp; Cancellation</a></li>
                    <li><a href="/cookies">Cookie Policy</a></li>
                </ul>
            </div>
            <div>
                <h4>Connect</h4>
                <ul>
                    <li>📧 <a href="mailto:<?= e(setting('contact_email','hello@spiritualmatrimony.com')) ?>"><?= e(setting('contact_email','hello@spiritualmatrimony.com')) ?></a></li>
                    <li>📞 <?= e(setting('contact_phone','+91 98XXX XXXXX')) ?></li>
                    <li>📍 <?= e(setting('contact_address','Rishikesh, India')) ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> <?= e(setting('site_name','Spiritual Matrimony')) ?>. Made with reverence.</span>
            <span class="om">ॐ शान्ति शान्ति शान्तिः</span>
        </div>
    </div>
</footer>
