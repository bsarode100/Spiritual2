<?php
$slug = 'child-safety';
?>
<section class="section-tight" style="padding: 4.5rem 0 2.5rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container text-center">
        <span class="eyebrow" style="display: inline-flex; align-items: center; gap: .45rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Trust &amp; Child Safety Standards
        </span>
        <h1 style="margin-top: .6rem;">Child Safety Standards &amp; CSAE Prevention Policy</h1>
        <p style="color: var(--c-ink-soft); font-size: 0.96rem; margin-top: 0.5rem;">
            <em>Last updated: January 2026 · Mandatory Public Standards Document</em>
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
                        <a href="/page/privacy" class="legal-nav-item">
                            <span class="nav-icon">🔒</span> Privacy Policy
                        </a>
                        <a href="/page/terms" class="legal-nav-item">
                            <span class="nav-icon">📜</span> Terms of Service
                        </a>
                        <a href="/page/refund-policy" class="legal-nav-item">
                            <span class="nav-icon">💳</span> Refund &amp; Cancellation
                        </a>
                        <a href="/page/cookie-policy" class="legal-nav-item">
                            <span class="nav-icon">🍪</span> Cookie Policy
                        </a>
                        <a href="/page/child-safety" class="legal-nav-item active">
                            <span class="nav-icon">🛡️</span> Child Safety Standards
                        </a>
                    </nav>
                </div>

                <!-- Dynamic Table of Contents -->
                <div class="legal-toc-card" id="legalTocCard">
                    <div class="legal-toc-header">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-maroon);"><path d="M4 6h16M4 12h10M4 18h14"/></svg>
                        <h4>On This Page</h4>
                    </div>
                    <nav class="legal-toc-list" id="legalTocList">
                        <!-- Populated dynamically via initLegalTOC() -->
                    </nav>
                </div>

                <!-- Grievance & Safety Officer Card -->
                <div class="legal-help-card">
                    <h5>Child Safety Officer</h5>
                    <p>Designated contact for child safety inquiries and law enforcement coordination:</p>
                    <p style="margin-bottom: .6rem; font-weight: 600; font-size: .88rem; color: var(--c-ink);">
                        📧 <a href="mailto:<?= e(setting('contact_email', 'support@spiritualshaadi.com')) ?>" style="color: inherit; text-decoration: none;"><?= e(setting('contact_email', 'support@spiritualshaadi.com')) ?></a>
                    </p>
                    <a href="/contact" class="btn btn-outline-primary btn-sm" style="width: 100%; text-align: center;">Submit Safety Report</a>
                </div>
            </aside>

            <!-- Main Legal Canvas (Framed in Luxury Document Card) -->
            <main class="legal-main-content">
                <article class="legal-document-card">
                    
                    <!-- Top Document Metadata Bar -->
                    <div class="legal-doc-header">
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            <span class="doc-meta-badge" style="background: #FEE2E2; color: #991B1B; border-color: rgba(153, 27, 27, 0.2);">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Strict Zero-Tolerance Standard
                            </span>
                            <span class="doc-meta-date">POCSO Act 2012 &amp; IT Rules 2021 Compliant</span>
                        </div>
                        <button type="button" class="doc-print-btn" onclick="window.print()" title="Print or save as PDF">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Print Document
                        </button>
                    </div>

                    <!-- Document Body with Wide, Comfortable Reading Canvas -->
                    <div class="prose prose-legal" id="legalDocBody">
                        
                        <div style="padding: 1.5rem; background: #FFF5F5; border-left: 4px solid #DC2626; border-radius: var(--r-sm); margin-bottom: 2rem;">
                            <h3 style="margin-top: 0; color: #DC2626; font-size: 1.2rem;">Absolute Zero-Tolerance Statement</h3>
                            <p style="margin-bottom: 0; font-size: 1.02rem; line-height: 1.7; color: #7F1D1D;">
                                <strong>SpiritualShaadi</strong> strictly enforces an <strong>absolute zero-tolerance policy</strong> against 
                                Child Sexual Abuse Material (CSAM) and Child Sexual Abuse and Exploitation (CSAE). Any attempt to exploit, harm, 
                                solicit, or endanger minors is strictly prohibited, results in immediate and permanent account termination, device and IP ban, 
                                and is reported to regional, national, and international law enforcement authorities without exception.
                            </p>
                        </div>

                        <h2>1. Platform Purpose &amp; Strict Age Eligibility (Adults Only)</h2>
                        <p>
                            SpiritualShaadi is an exclusive matrimonial and life-partnership platform created solely for consenting adults 
                            who are legally eligible for marriage. The platform is <strong>strictly prohibited to minors</strong>.
                        </p>
                        <ul>
                            <li><strong>Minimum Legal Marriage Age:</strong> In accordance with the Prohibition of Child Marriage Act, 2006, the Hindu Marriage Act, 1955, and the Special Marriage Act, 1954, registration is restricted strictly to individuals who have reached legal adulthood (minimum 18 years of age for females and 21 years of age for males).</li>
                            <li><strong>No Minor Accounts:</strong> Anyone under the age of 18 is strictly barred from registering, creating a profile, or accessing services on SpiritualShaadi.</li>
                            <li><strong>Immediate Account Revocation:</strong> If an account is discovered to belong to or represent a minor, it will be deleted immediately without notice.</li>
                        </ul>

                        <h2>2. Standards Against Child Sexual Abuse and Exploitation (CSAE)</h2>
                        <p>
                            In compliance with the <strong>Protection of Children from Sexual Offences (POCSO) Act, 2012</strong>, the 
                            <strong>Information Technology Act, 2000</strong>, the <strong>Information Technology (Intermediary Guidelines and Digital Media Ethics Code) Rules, 2021</strong>, 
                            and global child protection standards, the following activities are strictly forbidden:
                        </p>
                        <ul>
                            <li>Uploading, possessing, transmitting, sharing, soliciting, promoting, or distributing any Child Sexual Abuse Material (CSAM) in the form of photos, videos, audio, text, or external hyperlinks.</li>
                            <li>Any conduct aimed at grooming, contacting, enticing, sexually harassing, or exploiting children or minors.</li>
                            <li>Depicting, promoting, or facilitating child marriage, underage unions, or commercial sexual exploitation involving minors.</li>
                            <li>Using avatars, biographical text, or chat messages that suggest, glorify, or depict minors in any suggestive or sexualized manner.</li>
                        </ul>

                        <h2>3. Content Moderation &amp; Screening Practices</h2>
                        <p>
                            To maintain a safe and dharmic space for seekers, SpiritualShaadi applies multi-layered safety practices:
                        </p>
                        <ul>
                            <li><strong>Profile &amp; Photo Screening:</strong> All submitted profile photos, verification documents, and biographies are reviewed to ensure only adult profiles are permitted.</li>
                            <li><strong>Immediate Removal:</strong> Any content identified or flagged as violating child safety guidelines is removed and quarantined instantly.</li>
                            <li><strong>Permanent Exclusion:</strong> Any perpetrator attempting to share CSAM or exploit minors is permanently banned from SpiritualShaadi across all associated phone numbers, emails, devices, and IP addresses.</li>
                        </ul>

                        <h2>4. In-App Reporting Mechanisms</h2>
                        <p>
                            SpiritualShaadi provides multiple direct in-app mechanisms for users to quickly report any suspicious profile, message, or safety concern:
                        </p>
                        <ul>
                            <li><strong>In-App Profile Reporting:</strong> When viewing any member profile, click on the <strong>“Report / Safety Concern”</strong> action button, select <em>“Child Safety / Underage Concern”</em>, and submit details.</li>
                            <li><strong>In-App Chat Reporting:</strong> Users can flag and report suspicious or non-compliant messages directly from within the chat screen.</li>
                            <li><strong>Support &amp; Grievance Form:</strong> Reports can be submitted publicly at our <a href="/contact">Contact &amp; Grievance page</a>.</li>
                            <li><strong>Direct Safety Email:</strong> Immediate reports can be emailed directly to our safety mailbox at: <a href="mailto:<?= e(setting('contact_email', 'support@spiritualshaadi.com')) ?>"><strong><?= e(setting('contact_email', 'support@spiritualshaadi.com')) ?></strong></a>.</li>
                        </ul>
                        <p>
                            All child safety and underage reports receive highest-priority triage and are reviewed by our safety moderation team within 24 hours.
                        </p>

                        <h2>5. Mandatory Reporting to Law Enforcement &amp; Authorities</h2>
                        <p>
                            In full accordance with our legal obligations under Section 19 and 21 of the POCSO Act, 2012, and the Information Technology Rules:
                        </p>
                        <ul>
                            <li><strong>National Reporting:</strong> We promptly report all detected instances of CSAE or CSAM to the <strong>National Cyber Crime Reporting Portal</strong> (<a href="https://cybercrime.gov.in" target="_blank" rel="noopener">cybercrime.gov.in</a>) and the National Cyber Crime Helpline (<strong>1930</strong>) of the Government of India.</li>
                            <li><strong>Global Coordination:</strong> We report and cooperate with international organizations, including the <strong>National Center for Missing &amp; Exploited Children (NCMEC)</strong> and relevant international law enforcement bodies.</li>
                            <li><strong>Evidence Preservation:</strong> Relevant server logs, IP addresses, timestamps, and account details are preserved to assist authorized law enforcement officers in legal prosecution.</li>
                        </ul>

                        <h2>6. Designated Child Safety Point of Contact</h2>
                        <p>
                            Our designated point of contact is ready and able to speak about SpiritualShaadi's child sexual abuse material (CSAM) prevention practices and regulatory compliance:
                        </p>
                        <div style="padding: 1.4rem 1.6rem; background: var(--c-cream-2); border: 1px solid var(--c-line); border-radius: var(--r-sm); margin-top: 1rem;">
                            <p style="margin: 0.3rem 0;"><strong>Designated Safety &amp; Grievance Officer:</strong> <?= e(setting('site_name', 'SpiritualShaadi')) ?> Safety &amp; Compliance Team</p>
                            <p style="margin: 0.3rem 0;"><strong>Dedicated Contact Email:</strong> <a href="mailto:<?= e(setting('contact_email', 'support@spiritualshaadi.com')) ?>"><strong><?= e(setting('contact_email', 'support@spiritualshaadi.com')) ?></strong></a></p>
                            <p style="margin: 0.3rem 0;"><strong>Platform:</strong> SpiritualShaadi (<a href="https://spiritualshaadi.com">https://spiritualshaadi.com</a>)</p>
                            <p style="margin: 0.3rem 0;"><strong>Jurisdiction:</strong> India</p>
                            <p style="margin: 0.3rem 0; font-size: 0.88rem; color: var(--c-muted);">Inquiries regarding CSAM prevention, user reports, or legal compliance are addressed promptly.</p>
                        </div>

                    </div>

                    <!-- Document Sign-off & Seal -->
                    <div class="legal-doc-footer">
                        <div class="legal-seal">
                            <div class="om-seal">ॐ</div>
                            <div>
                                <div class="seal-title"><?= e(setting('site_name', 'SpiritualShaadi')) ?> Child Protection Standard</div>
                                <div class="seal-sub">Zero-Tolerance · Rapid Enforcement · Proactive Law Enforcement Cooperation</div>
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
