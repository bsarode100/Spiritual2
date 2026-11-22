<?php
/** @var array $page */
$slug = $page['slug'] ?? '';
$isAbout = in_array($slug, ['about', 'about-us', 'our-story'], true);
?>

<?php if ($isAbout): ?>
    <!-- ============================================================== -->
    <!-- OUR STORY / ABOUT US — SACRED STORYTELLING LAYOUT              -->
    <!-- ============================================================== -->
    <section class="about-hero-section">
        <div class="container text-center">
            <span class="eyebrow" style="display: inline-flex; align-items: center; gap: .45rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                Sacred Genesis &amp; Mission
            </span>
            <h1 style="margin: 0.8rem 0 1.2rem; font-size: clamp(2.2rem, 5vw, 3.4rem); letter-spacing: -0.02em;">
                Where Devotion Meets <em style="color: var(--c-saffron); font-family: var(--f-display); font-style: italic;">Destiny</em>
            </h1>
            <p style="font-size: clamp(1.1rem, 2vw, 1.25rem); color: var(--c-ink-soft); max-width: 760px; margin: 0 auto; line-height: 1.75;">
                SpiritualShaadi is a dedicated matrimonial sanctuary created for sincere seekers who believe marriage is not merely a social arrangement, but a sacred spiritual partnership — <strong>Grihastha Dharma</strong>.
            </p>
        </div>
    </section>

    <section class="section" style="padding-top: 1rem;">
        <div class="container">
            
            <!-- The Genesis Narrative -->
            <div class="about-story-grid">
                <div class="about-story-card">
                    <div>
                        <span class="eyebrow" style="color: var(--c-maroon);">The Genesis</span>
                        <h2>The Question Matrimony Forgot to Ask</h2>
                        <p>
                            Modern matrimonial websites assess candidates like commercial resumes — sorting human beings by income brackets, corporate titles, and external glamour. Yet for devotees, sadhakas, yogis, and spiritual practitioners, the single most defining question of life is:
                        </p>
                        <p style="font-family: var(--f-display); font-size: 1.25rem; color: var(--c-maroon); font-style: italic; border-left: 3px solid var(--c-rose); padding-left: 1.2rem; margin: 1.4rem 0;">
                            "Do we walk the same spiritual path? Will the sacredness of morning sadhana, pure nourishment, and devotion be honored in our home?"
                        </p>
                        <p>
                            SpiritualShaadi was created to answer this profound yearning. We built a respectful, conscious sanctuary where individuals and families unite on the foundation of shared dharma, lineage, lifestyle, and spiritual values.
                        </p>
                    </div>
                </div>

                <div class="about-story-card" style="background: linear-gradient(135deg, #FFFDFB 0%, #FAF3EB 100%);">
                    <div>
                        <span class="eyebrow" style="color: var(--c-gold);">Grihastha Dharma</span>
                        <h2>The Sacred Ashram of Family Life</h2>
                        <p>
                            In timeless Vedic philosophy, <em>Grihastha Ashram</em> is revered as the greatest ashram — the noble pillar supporting society where two souls help each other navigate the world and attain spiritual elevation together.
                        </p>
                        <p>
                            Whether you chant the Maha-Mantra, practice Kriya Yoga, sit in Vipassana meditation, study Advaita Vedanta, or follow the compassionate tenets of Jainism and Sikhism — your devotion is not an afterthought here. It is the sacred cornerstone of your union.
                        </p>
                        <p>
                            We provide thoughtful filters for daily sadhana habits, guru lineages, dietary practices (Sattvic, Vegetarian, Vegan), and philosophical perspectives so you can find your spiritual equal effortlessly.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sacred Vedic Quote Box -->
            <div class="about-quote-box">
                <div class="about-quote-sanskrit">
                    यथा चित्तं तथा वाचो यथा वाचस्तथा क्रियाः ।<br>
                    चित्ते वाचि क्रियायां च साधूनामेकरूपता ॥
                </div>
                <div class="about-quote-trans">
                    "As is the mind, so is the speech; as is the speech, so is the action. In mind, speech, and action, the noble ones are in complete harmony."
                </div>
                <span class="about-quote-source">— Traditional Subhashita</span>
            </div>

            <!-- 4 Dharmic Pillars Grid -->
            <div class="text-center" style="margin-bottom: 2rem;">
                <span class="eyebrow">Guiding Principles</span>
                <h2 style="font-family: var(--f-display); color: var(--c-ink); font-size: clamp(1.8rem, 3.5vw, 2.5rem);">Our Four Dharmic Pillars</h2>
            </div>

            <div class="about-pillars-grid">
                <div class="about-pillar-card">
                    <div class="about-pillar-icon">🌸</div>
                    <h4>Sadhana Over Status</h4>
                    <p>We honor inner character, dedication to spiritual practice, humility, and truthful living above worldly ostentation.</p>
                </div>

                <div class="about-pillar-card">
                    <div class="about-pillar-icon">📿</div>
                    <h4>Authentic Lineages</h4>
                    <p>Welcoming sincere seekers across Sanatana Dharma, ISKCON, Art of Living, Isha, Brahma Kumaris, Advaita, Sikhism, Jainism, Buddhism, and all yogic paths.</p>
                </div>

                <div class="about-pillar-card">
                    <div class="about-pillar-icon">🛡️</div>
                    <h4>Sanctuary of Reverence</h4>
                    <p>100% human-reviewed profiles, zero tolerance for misconduct, optional ID/selfie verification, and discreet photo privacy controls.</p>
                </div>

                <div class="about-pillar-card">
                    <div class="about-pillar-icon">🕊️</div>
                    <h4>Sattvic Living</h4>
                    <p>Deep compatibility filters for pure diets (Sattvic, Vegetarian, Vegan), fasting days, sacred literature, and peaceful mindful lifestyles.</p>
                </div>
            </div>

            <!-- Community Highlights Bar -->
            <div class="about-stats-bar">
                <div class="about-stats-grid">
                    <div class="about-stat-item">
                        <div class="about-stat-number">18+</div>
                        <div class="about-stat-label">Spiritual Lineages Welcomed</div>
                    </div>
                    <div class="about-stat-item">
                        <div class="about-stat-number">100%</div>
                        <div class="about-stat-label">Human-Reviewed Profiles</div>
                    </div>
                    <div class="about-stat-item">
                        <div class="about-stat-number">1,200+</div>
                        <div class="about-stat-label">Sacred Unions &amp; Connections</div>
                    </div>
                    <div class="about-stat-item">
                        <div class="about-stat-number">0%</div>
                        <div class="about-stat-label">Third-Party Data Selling</div>
                    </div>
                </div>
            </div>

            <?php if (!empty($page['body']) && !str_contains($page['body'], 'Spiritual Matrimony was born from a simple observation')): ?>
                <!-- Custom Editorial Content (if modified by admin) -->
                <div class="legal-document-card" style="margin: 3rem 0;">
                    <div class="prose prose-legal">
                        <?= format_page_body($page['body']) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Invitation / CTA Box -->
            <div class="about-cta-box">
                <span class="eyebrow" style="color: var(--c-saffron);">Begin With A Pure Heart</span>
                <h2 style="font-family: var(--f-display); font-size: clamp(1.8rem, 3.5vw, 2.6rem); color: var(--c-ink); margin: 0.6rem 0 1.2rem;">
                    Find Your Spiritual Life Partner Today
                </h2>
                <p style="font-size: 1.15rem; color: var(--c-ink-soft); max-width: 600px; margin: 0 auto 2rem; line-height: 1.7;">
                    Join a community of sincere sadhakas dedicated to building a serene, dharma-rooted marriage.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="/browse" class="btn btn-primary btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Explore Spiritual Seekers
                    </a>
                    <a href="/register" class="btn btn-gold btn-lg">
                        Create Free Profile
                    </a>
                </div>
            </div>

        </div>
    </section>

<?php else: ?>
    <!-- ============================================================== -->
    <!-- LEGAL & POLICY PAGES — 2-COLUMN LUXURY DESKTOP GRID            -->
    <!-- ============================================================== -->
    <section class="section-tight" style="padding: 4.5rem 0 2.5rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
        <div class="container text-center">
            <span class="eyebrow" style="display: inline-flex; align-items: center; gap: .45rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Trust, Privacy &amp; Legal Transparency
            </span>
            <h1 style="margin-top: .6rem;"><?= e($page['title']) ?></h1>
            <p style="color: var(--c-ink-soft); font-size: 0.96rem; margin-top: 0.5rem;">
                <em>Published in accordance with the Information Technology Act, 2000 &amp; DPDP Act, 2023.</em>
            </p>
        </div>
    </section>

    <section class="section" style="padding-top: 0;">
        <div class="container">
            <div class="legal-layout-grid">
                
                <!-- Sticky Left Sidebar (Desktop) / Horizontal Pill Tabs (Mobile) -->
                <aside class="legal-sidebar">
                    <!-- Policy Navigation Card -->
                    <div class="legal-nav-card">
                        <div class="legal-nav-header">
                            <span class="legal-nav-badge">Platform</span>
                            <h4>Policies</h4>
                        </div>
                        <nav class="legal-nav-list">
                            <a href="/page/privacy" class="legal-nav-item <?= in_array($slug, ['privacy', 'privacy-policy'], true) ? 'active' : '' ?>">
                                <span class="nav-icon">🔒</span> Privacy Policy
                            </a>
                            <a href="/page/terms" class="legal-nav-item <?= in_array($slug, ['terms', 'terms-and-condition'], true) ? 'active' : '' ?>">
                                <span class="nav-icon">📜</span> Terms of Service
                            </a>
                            <a href="/page/refund-policy" class="legal-nav-item <?= in_array($slug, ['refund-policy', 'refund'], true) ? 'active' : '' ?>">
                                <span class="nav-icon">💳</span> Refund &amp; Cancellation
                            </a>
                            <a href="/page/cookie-policy" class="legal-nav-item <?= in_array($slug, ['cookie-policy', 'cookies'], true) ? 'active' : '' ?>">
                                <span class="nav-icon">🍪</span> Cookie Policy
                            </a>
                            <a href="/page/child-safety" class="legal-nav-item <?= in_array($slug, ['child-safety', 'safety-standards', 'csae-policy'], true) ? 'active' : '' ?>">
                                <span class="nav-icon">🛡️</span> Child Safety Standards
                            </a>
                        </nav>
                    </div>

                    <!-- Dynamic Table of Contents (Auto-populated by JS from h2/h3) -->
                    <div class="legal-toc-card" id="legalTocCard">
                        <div class="legal-toc-header">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-maroon);"><path d="M4 6h16M4 12h10M4 18h14"/></svg>
                            <h4>On This Page</h4>
                        </div>
                        <nav class="legal-toc-list" id="legalTocList">
                            <!-- Populated dynamically via initLegalTOC() -->
                        </nav>
                    </div>

                    <!-- Grievance Officer & Contact Card -->
                    <div class="legal-help-card">
                        <h5>Grievance Officer</h5>
                        <p>Under the DPDP Act 2023 &amp; IT Rules, our Grievance Officer addresses all user concerns promptly:</p>
                        <p style="margin-bottom: .6rem; font-weight: 600; font-size: .88rem; color: var(--c-ink);">
                            📧 <a href="mailto:<?= e(setting('contact_email', 'support@spiritualshaadi.com')) ?>" style="color: inherit; text-decoration: none;"><?= e(setting('contact_email', 'support@spiritualshaadi.com')) ?></a>
                        </p>
                        <a href="/contact" class="btn btn-outline-primary btn-sm" style="width: 100%; text-align: center;">Contact Support</a>
                    </div>
                </aside>

                <!-- Main Legal Canvas (Framed in Luxury Document Card) -->
                <main class="legal-main-content">
                    <article class="legal-document-card">
                        
                        <!-- Top Document Metadata Bar -->
                        <div class="legal-doc-header">
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <span class="doc-meta-badge">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    Official Policy Document
                                </span>
                                <span class="doc-meta-date">Effective: January 2026</span>
                            </div>
                            <button type="button" class="doc-print-btn" onclick="window.print()" title="Print or save as PDF">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                Print Document
                            </button>
                        </div>

                        <!-- Document Body with Wide, Comfortable Reading Canvas -->
                        <div class="prose prose-legal" id="legalDocBody">
                            <?= format_page_body($page['body']) ?>
                        </div>

                        <!-- Document Sign-off & Seal -->
                        <div class="legal-doc-footer">
                            <div class="legal-seal">
                                <div class="om-seal">ॐ</div>
                                <div>
                                    <div class="seal-title"><?= e(setting('site_name', 'SpiritualShaadi')) ?> Trust &amp; Safety</div>
                                    <div class="seal-sub">Dharmic Integrity · Strict Legal Compliance · Complete Privacy</div>
                                </div>
                            </div>
                            <a href="#top" class="back-to-top-link" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                                ↑ Back to Top
                            </a>
                        </div>

                    </article>
                </main>

            </div>
        </div>
    </section>
<?php endif; ?>
