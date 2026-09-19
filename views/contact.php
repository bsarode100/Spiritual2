<section class="section-tight" style="padding: 5.5rem 0 3.5rem; background: radial-gradient(ellipse at 50% 30%, rgba(247, 214, 220, 0.45) 0%, rgba(239, 232, 246, 0.35) 45%, transparent 70%); text-align: center;">
    <div class="container-sm">
        <span class="eyebrow">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Sacred Dialogue
        </span>
        <h1>Get in <em style="color: var(--c-saffron); font-family: var(--f-display);">touch</em></h1>
        <p style="font-size: 1.15rem; color: var(--c-ink-soft); max-width: 580px; margin: 0.8rem auto 0; line-height: 1.65;">
            We read every message with reverence and care. Reach out for any questions, guidance, or assistance.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: 3.5rem; align-items: start;">
            <aside class="admin-card" style="padding: 2.4rem;">
                <h3 style="color: var(--c-ink); margin-bottom: 1.4rem;">Our Office &amp; Sanctuary</h3>
                <p style="color: var(--c-ink-soft); margin-bottom: .8rem;">📍 <?= e(setting('contact_address','Rishikesh, Uttarakhand, India')) ?></p>
                <p style="color: var(--c-ink-soft); margin-bottom: .8rem;">📞 <?= e(setting('contact_phone','+91 98XXX XXXXX')) ?></p>
                <p style="color: var(--c-ink-soft); margin-bottom: 1.4rem;">📧 <a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email','hello@spiritualmatrimony.com')) ?></a></p>
                <div class="deco-divider"><span class="om">ॐ</span></div>
                <p style="font-style: italic; font-family: var(--f-display); font-size: 1.25rem; color: var(--c-maroon); line-height: 1.5; margin-bottom: 0;">
                    "The guest is divine." — Taittiriya Upanishad
                </p>
            </aside>

            <form method="post" action="/contact" class="admin-card" style="padding: 2.8rem 2.2rem;">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="field"><label>Your Name</label><input type="text" name="name" placeholder="Full name" required></div>
                    <div class="field"><label>Email Address</label><input type="email" name="email" placeholder="you@example.com" required></div>
                    <div class="field"><label>Phone Number</label><input type="tel" name="phone" placeholder="+91 ..."></div>
                    <div class="field"><label>Subject</label><input type="text" name="subject" placeholder="How can we assist you?"></div>
                    <div class="field full"><label>Your Message</label><textarea name="message" rows="6" placeholder="Write your message here..." required></textarea></div>
                </div>
                <button class="btn btn-primary btn-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    Send Message
                </button>
                <p style="margin-top: 1.2rem; font-size: .84rem; color: var(--c-muted); margin-bottom: 0;">
                    Your details are protected with complete privacy. We will never share them without your consent.
                </p>
            </form>
        </div>
    </div>
</section>
