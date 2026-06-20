<section class="section-tight" style="padding: 5rem 0 3rem; background: linear-gradient(180deg, var(--c-cream-2), transparent);">
    <div class="container text-center">
        <span class="eyebrow">Write to us</span>
        <h1>Get in <em style="color: var(--c-saffron); font-family: var(--f-display);">touch</em></h1>
        <p style="font-size: 1.1rem; color: var(--c-ink-soft); max-width: 600px; margin: 0 auto;">We read every message. Reach out and we'll write back with reverence.</p>
    </div>
</section>
<section class="section"><div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: 3rem; align-items: start;">
        <aside>
            <h3 style="color: var(--c-maroon);">Our office</h3>
            <p style="color: var(--c-ink-soft);">📍 <?= e(setting('contact_address','Rishikesh, India')) ?></p>
            <p style="color: var(--c-ink-soft);">📞 <?= e(setting('contact_phone','+91 98XXX XXXXX')) ?></p>
            <p style="color: var(--c-ink-soft);">📧 <a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email','hello@spiritualmatrimony.com')) ?></a></p>
            <div class="deco-divider"><span class="om">ॐ</span></div>
            <p style="font-style: italic; font-family: var(--f-display); font-size: 1.15rem; color: var(--c-maroon);">"The guest is god." — Taittiriya Upanishad</p>
        </aside>

        <form method="post" action="/contact" class="admin-card">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="field"><label>Your name</label><input type="text" name="name" required></div>
                <div class="field"><label>Email</label><input type="email" name="email" required></div>
                <div class="field"><label>Phone</label><input type="tel" name="phone"></div>
                <div class="field"><label>Subject</label><input type="text" name="subject"></div>
                <div class="field full"><label>Message</label><textarea name="message" rows="6" required></textarea></div>
            </div>
            <button class="btn btn-primary btn-lg">Send Message</button>
            <p style="margin-top: 1rem; font-size: .8rem; color: var(--c-ink-soft);">
                Your details are handled per our
                <a href="/page/privacy-policy">Privacy Policy</a>. We will never share them with other members.
            </p>
        </form>
    </div>
</div></section>
