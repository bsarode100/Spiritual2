<?php
$contactPhone = setting('contact_phone', '+91 7744922099');
$contactEmail = setting('contact_email', 'support@spiritualshaadi.com');
$contactAddress = setting('contact_address', 'At Wagdari, Tq Deoni, Dist Latur, Maharashtra. Pincode - 413519.');
$waPhone = preg_replace('/[^+0-9]/', '', $contactPhone);
$cleanWaPhone = ltrim($waPhone, '+');
?>
<section class="section-tight" style="padding: 5rem 0 3rem; background: radial-gradient(ellipse at 50% 25%, rgba(247, 214, 220, 0.45) 0%, rgba(239, 232, 246, 0.35) 45%, transparent 70%); text-align: center;">
    <div class="container-sm">
        <span class="eyebrow" style="display: inline-flex; align-items: center; gap: .45rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Sacred Dialogue &amp; Support
        </span>
        <h1 style="margin: .8rem 0 1rem; font-size: clamp(2.2rem, 5vw, 3.2rem);">
            Get in <em style="color: var(--c-saffron); font-family: var(--f-display); font-style: italic;">touch</em>
        </h1>
        <p style="font-size: 1.15rem; color: var(--c-ink-soft); max-width: 620px; margin: 0 auto; line-height: 1.7;">
            We read every message with reverence and care. Reach out for any questions, verification guidance, or membership assistance.
        </p>
    </div>
</section>

<section class="section" style="padding-top: 0.5rem;">
    <div class="container">
        <div class="contact-layout-grid">
            
            <!-- Left Aside: Channels, Direct WhatsApp & Office Details -->
            <aside class="contact-aside">
                
                <!-- Direct WhatsApp Assistance Card -->
                <div class="contact-card contact-whatsapp-card">
                    <div style="display: flex; align-items: center; gap: .75rem; margin-bottom: .85rem;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #25D366; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            💬
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 1.05rem; color: #166534; font-weight: 700;">Direct WhatsApp Help</h4>
                            <span style="font-size: .8rem; color: #15803D;">Fastest response (15–30 mins)</span>
                        </div>
                    </div>
                    <p style="font-size: .88rem; color: #166534; line-height: 1.5; margin-bottom: 1.2rem;">
                        Need quick guidance regarding profile setup, verification, or payments? Connect directly with our concierge team.
                    </p>
                    <a href="https://wa.me/<?= e($cleanWaPhone) ?>?text=Namaste%20SpiritualShaadi%20team,%20I%20need%20assistance" target="_blank" rel="noopener" class="contact-whatsapp-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        Chat on WhatsApp
                    </a>
                </div>

                <!-- Office & Channels Card -->
                <div class="contact-card">
                    <h4 style="margin: 0 0 1.2rem; font-size: 1.15rem; color: var(--c-ink); font-family: var(--f-display);">Office &amp; Sanctuary</h4>
                    
                    <div class="contact-channel-item">
                        <div class="contact-channel-icon">📍</div>
                        <div class="contact-channel-info">
                            <h5>Registered Office</h5>
                            <p><?= e($contactAddress) ?></p>
                        </div>
                    </div>

                    <div class="contact-channel-item">
                        <div class="contact-channel-icon">📞</div>
                        <div class="contact-channel-info">
                            <h5>Telephone Helpline</h5>
                            <p><a href="tel:<?= e($waPhone) ?>"><?= e($contactPhone) ?></a></p>
                        </div>
                    </div>

                    <div class="contact-channel-item">
                        <div class="contact-channel-icon">📧</div>
                        <div class="contact-channel-info">
                            <h5>Support Email</h5>
                            <p><a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a></p>
                        </div>
                    </div>

                    <div class="contact-channel-item">
                        <div class="contact-channel-icon">⏰</div>
                        <div class="contact-channel-info">
                            <h5>Support Timings</h5>
                            <p>Monday – Saturday<br><span style="font-size: .88rem; color: var(--c-muted);">9:00 AM – 7:00 PM IST</span></p>
                        </div>
                    </div>
                </div>

                <!-- Upanishadic Blessing Card -->
                <div class="contact-card" style="background: linear-gradient(135deg, #FFF9F3 0%, #FAF3EB 100%); border-left: 4px solid var(--c-gold);">
                    <div style="font-size: 1.4rem; color: var(--c-gold); line-height: 1; margin-bottom: .4rem;">ॐ</div>
                    <p style="font-family: var(--f-display); font-size: 1.2rem; color: var(--c-maroon); font-style: italic; line-height: 1.5; margin-bottom: .6rem;">
                        "अतिथिदेवो भव"
                    </p>
                    <p style="font-size: .88rem; color: var(--c-ink-soft); line-height: 1.6; margin: 0;">
                        "The guest is divine." — <em>Taittiriya Upanishad</em>.<br>
                        Every seeker reaching out to us is treated with dignity, reverence, and complete confidentiality.
                    </p>
                </div>

            </aside>

            <!-- Right Column: Interactive Contact Message Form -->
            <main>
                <div class="contact-card" style="padding: clamp(2rem, 4vw, 3rem);">
                    <div style="margin-bottom: 1.6rem;">
                        <h3 style="font-family: var(--f-display); font-size: 1.7rem; color: var(--c-ink); margin: 0 0 .4rem;">
                            Send Us a Reverent Message
                        </h3>
                        <p style="color: var(--c-ink-soft); font-size: .95rem; margin: 0;">
                            Have a specific inquiry? Click a topic to auto-fill or type your query below.
                        </p>
                    </div>

                    <!-- Quick Subject Topic Selection Pills -->
                    <div class="quick-subject-pills">
                        <button type="button" class="subject-pill" data-subject="Membership &amp; Package Inquiry">🌟 Membership Plans</button>
                        <button type="button" class="subject-pill" data-subject="Profile Verification Assistance">🛡️ Profile Verification</button>
                        <button type="button" class="subject-pill" data-subject="Technical &amp; App Support">⚙️ App / Technical Help</button>
                        <button type="button" class="subject-pill" data-subject="General Inquiry &amp; Blessings">🙏 General Inquiry</button>
                    </div>

                    <form method="post" action="/contact">
                        <?= csrf_field() ?>
                        <div class="form-grid">
                            <div class="field">
                                <label style="font-weight: 600; font-size: .88rem; color: var(--c-ink);">Your Full Name *</label>
                                <input type="text" name="name" class="glass-control" placeholder="Enter your full name" required>
                            </div>
                            <div class="field">
                                <label style="font-weight: 600; font-size: .88rem; color: var(--c-ink);">Email Address *</label>
                                <input type="email" name="email" class="glass-control" placeholder="you@example.com" required>
                            </div>
                            <div class="field">
                                <label style="font-weight: 600; font-size: .88rem; color: var(--c-ink);">Phone Number (Optional)</label>
                                <input type="tel" name="phone" class="glass-control" placeholder="+91 98765 43210">
                            </div>
                            <div class="field">
                                <label style="font-weight: 600; font-size: .88rem; color: var(--c-ink);">Subject *</label>
                                <input type="text" name="subject" id="contactSubjectInput" class="glass-control" placeholder="How can we assist you?" required>
                            </div>
                            <div class="field full">
                                <label style="font-weight: 600; font-size: .88rem; color: var(--c-ink);">Your Message *</label>
                                <textarea name="message" rows="6" class="glass-control" placeholder="Write your message with as much detail as you'd like..." required></textarea>
                            </div>
                        </div>

                        <div style="margin-top: 1.8rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.2rem;">
                            <button class="btn btn-primary btn-lg" style="padding: 0.9rem 2.2rem;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                Send Reverent Message
                            </button>
                            <span style="font-size: .84rem; color: var(--c-muted); display: inline-flex; align-items: center; gap: .4rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                100% Privacy &amp; Data Protection
                            </span>
                        </div>
                    </form>
                </div>
            </main>

        </div>
    </div>
</section>
