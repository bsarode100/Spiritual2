<?php /** @var int $identity_price, $selfie_price; @var array|null $existing */
$loggedIn = Auth::check();
$status = $existing['status'] ?? null;
$needsDocs = $existing && in_array($status, ['pending_upload', 'rejected'], true);
$statusLabels = [
    'pending_payment' => ['gold',  'Awaiting payment'],
    'pending_upload'  => ['gold',  'Documents needed'],
    'pending_review'  => ['gold',  'Under review'],
    'approved'        => ['green', 'Approved'],
    'rejected'        => ['red',   'Needs resubmission'],
];
?>
<section class="section-tight" style="padding: 5rem 0 3rem;">
<div class="container">
    <div class="section-head">
        <span class="eyebrow">Trust service</span>
        <h1>Get the Verified badge</h1>
        <p class="lead">Share a live selfie (photo or short video) and any government ID. Our team reviews every submission by hand — the badge tells other seekers your profile is genuinely you. Verification is independent from membership plans.</p>
    </div>

    <?php if ($existing): [$pillCls, $pillLabel] = $statusLabels[$status] ?? ['gold', $status]; ?>
        <div class="admin-card mb-4">
            <h3>Your verification request</h3>
            <div class="info-row"><span class="k">Tier</span><span class="v"><?= $existing['tier'] === 'selfie' ? 'Selfie + Identity' : 'Identity' ?></span></div>
            <div class="info-row"><span class="k">Status</span><span class="v"><span class="pill <?= $pillCls ?>"><?= e($pillLabel) ?></span></span></div>
            <div class="info-row"><span class="k">Requested</span><span class="v"><?= e(date('M j, Y', strtotime($existing['created_at']))) ?></span></div>

            <?php if ($status === 'pending_payment'): ?>
                <p style="color: var(--c-muted); margin: .8rem 0 0;">Your payment is pending — complete it to continue.</p>
                <a href="/checkout/verification/<?= (int)$existing['id'] ?>" class="btn btn-primary btn-sm" style="margin-top: .6rem;">Complete Payment</a>
            <?php elseif ($status === 'pending_review'): ?>
                <p style="color: var(--c-muted); margin: .8rem 0 0;">Documents received<?= $existing['submitted_at'] ? ' on ' . e(date('M j, Y g:i a', strtotime($existing['submitted_at']))) : '' ?>. Our team usually completes reviews within 24–48 hours — we'll email you either way.</p>
            <?php elseif ($status === 'approved'): ?>
                <p style="color: var(--c-muted); margin: .8rem 0 0;">🎉 Your profile now carries the <strong><?= $existing['tier'] === 'selfie' ? 'ID + Selfie Verified' : 'ID Verified' ?></strong> badge everywhere it appears on the site.</p>
            <?php elseif ($status === 'rejected'): ?>
                <div style="background: #FDECEC; border-radius: 8px; padding: .8rem 1rem; margin-top: .8rem;">
                    <strong style="color: #6E1F1F;">Your submission was not approved.</strong>
                    <?php if (!empty($existing['reject_reason'])): ?>
                        <p style="margin: .3rem 0 0; color: #6E1F1F;">Reason: <?= e($existing['reject_reason']) ?></p>
                    <?php endif; ?>
                    <p style="margin: .3rem 0 0; color: #6E1F1F;">You can fix the issue and resubmit below — no extra payment needed.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($needsDocs): ?>
        <!-- ================= DOCUMENT SUBMISSION ================= -->
        <div class="admin-card mb-4" id="submit-docs">
            <h3><?= $status === 'rejected' ? 'Resubmit your documents' : 'Submit your documents' ?></h3>
            <p style="color: var(--c-muted);">Your documents are stored privately, visible only to our review team, and deleted after the review decision. They are never shown on your profile.</p>

            <form method="post" action="/verification/<?= (int)$existing['id'] ?>/documents" enctype="multipart/form-data" id="verify-form">
                <?= csrf_field() ?>

                <div class="field" style="margin-bottom: 1rem;">
                    <label><strong>Step 1 — Government ID</strong></label>
                    <select name="id_doc_type" required style="margin-bottom: .5rem;">
                        <option value="">Select ID type…</option>
                        <option value="aadhaar">Aadhaar Card</option>
                        <option value="pan">PAN Card</option>
                        <option value="passport">Passport</option>
                        <option value="driving_licence">Driving Licence</option>
                        <option value="voter_id">Voter ID</option>
                    </select>
                    <input type="file" name="id_doc" accept="image/jpeg,image/png,image/webp,application/pdf" required>
                    <small style="color: var(--c-muted);">Clear photo or PDF, max 4MB. Make sure your name and photo are readable. You may mask the ID number — we verify the name, photo and date of birth.</small>
                </div>

                <?php if ($existing['tier'] === 'selfie'): ?>
                <div class="field">
                    <label><strong>Step 2 — Live selfie</strong></label>
                    <p style="color: var(--c-muted); font-size: .9rem; margin: .2rem 0 .6rem;">Use your camera below so we know it's really you — a photo, or a short video where you turn your head left and right.</p>

                    <div id="cam-wrap" style="display: none; max-width: 420px;">
                        <video id="cam" autoplay playsinline muted style="width: 100%; border-radius: 10px; background: #000;"></video>
                        <div class="flex gap-1" style="margin-top: .5rem; flex-wrap: wrap;">
                            <button type="button" class="btn btn-primary btn-sm" id="snap">📷 Capture Photo</button>
                            <button type="button" class="btn btn-gold btn-sm" id="rec">🎥 Record 5s Video</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm" id="cam-start">Open Camera</button>

                    <div id="preview-wrap" style="display: none; max-width: 420px; margin-top: .6rem;">
                        <img id="preview-img" style="display: none; width: 100%; border-radius: 10px;" alt="Selfie preview">
                        <video id="preview-vid" controls playsinline style="display: none; width: 100%; border-radius: 10px;"></video>
                        <p style="color: var(--c-muted); font-size: .85rem; margin: .3rem 0 0;">Looks good? Submit below — or retake with the buttons above.</p>
                    </div>

                    <input type="file" name="selfie" id="selfie-fallback" accept="image/*,video/webm,video/mp4" capture="user" style="margin-top: .5rem;">
                    <small style="color: var(--c-muted);">No camera access? Use the file picker — on phones it opens the front camera directly.</small>
                </div>
                <?php endif; ?>

                <button class="btn btn-primary" style="margin-top: 1.2rem;">Submit for Review</button>
            </form>
        </div>

        <?php if ($existing['tier'] === 'selfie'): ?>
        <script>
        (function () {
            const form = document.getElementById('verify-form');
            const wrap = document.getElementById('cam-wrap');
            const startBtn = document.getElementById('cam-start');
            const video = document.getElementById('cam');
            const fallback = document.getElementById('selfie-fallback');
            const previewWrap = document.getElementById('preview-wrap');
            const previewImg = document.getElementById('preview-img');
            const previewVid = document.getElementById('preview-vid');
            let stream = null;

            function setCaptured(blob, isVideo) {
                const file = new File([blob], isVideo ? 'selfie.webm' : 'selfie.jpg', { type: blob.type });
                const dt = new DataTransfer();
                dt.items.add(file);
                fallback.files = dt.files;              // ride the normal form submit
                const url = URL.createObjectURL(blob);
                previewWrap.style.display = 'block';
                previewImg.style.display = isVideo ? 'none' : 'block';
                previewVid.style.display = isVideo ? 'block' : 'none';
                (isVideo ? previewVid : previewImg).src = url;
            }

            startBtn.addEventListener('click', async () => {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                    video.srcObject = stream;
                    wrap.style.display = 'block';
                    startBtn.style.display = 'none';
                } catch (e) {
                    alert('Could not open the camera — please use the file picker instead.');
                }
            });

            document.getElementById('snap').addEventListener('click', () => {
                const c = document.createElement('canvas');
                c.width = video.videoWidth; c.height = video.videoHeight;
                c.getContext('2d').drawImage(video, 0, 0);
                c.toBlob(b => b && setCaptured(b, false), 'image/jpeg', 0.9);
            });

            document.getElementById('rec').addEventListener('click', function () {
                if (!stream || this.disabled) return;
                if (typeof MediaRecorder === 'undefined') { alert('Video recording is not supported in this browser — capture a photo instead.'); return; }
                const rec = new MediaRecorder(stream, { mimeType: MediaRecorder.isTypeSupported('video/webm;codecs=vp9') ? 'video/webm;codecs=vp9' : 'video/webm' });
                const chunks = [];
                rec.ondataavailable = e => chunks.push(e.data);
                rec.onstop = () => {
                    setCaptured(new Blob(chunks, { type: 'video/webm' }), true);
                    this.disabled = false; this.textContent = '🎥 Record 5s Video';
                };
                rec.start();
                this.disabled = true;
                let left = 5;
                this.textContent = 'Recording… ' + left + 's';
                const t = setInterval(() => {
                    left--;
                    if (left <= 0) { clearInterval(t); rec.stop(); }
                    else this.textContent = 'Recording… ' + left + 's';
                }, 1000);
            });

            form.addEventListener('submit', () => {
                if (stream) stream.getTracks().forEach(t => t.stop());
            });
        })();
        </script>
        <?php endif; ?>

    <?php elseif (!$existing || in_array($status, ['approved', 'rejected'], true)): ?>
        <!-- ================= TIER CARDS ================= -->
        <div class="pkg-grid">
            <div class="pkg">
                <div class="pkg-name">Identity Verification</div>
                <div class="pkg-tag">Government ID reviewed by our team.</div>
                <div class="pkg-price"><?= $identity_price > 0 ? '<small>Rs</small>' . number_format($identity_price, 0) : 'Free' ?></div>
                <ul class="pkg-features">
                    <li>Any govt ID — Aadhaar, PAN, Passport, DL, Voter ID</li>
                    <li>Hand-reviewed within 24–48 hours</li>
                    <li>“ID Verified” badge on your profile</li>
                    <li>Documents deleted after review</li>
                </ul>
                <?php if ($loggedIn): ?>
                    <form method="post" action="/verification/start">
                        <?= csrf_field() ?>
                        <input type="hidden" name="tier" value="identity">
                        <button class="btn btn-primary btn-block">Start Verification</button>
                    </form>
                <?php else: ?>
                    <a href="/register" class="btn btn-primary btn-block">Sign Up</a>
                <?php endif; ?>
            </div>

            <div class="pkg featured">
                <div class="pkg-name">Selfie + Identity</div>
                <div class="pkg-tag">Live selfie match — the strongest trust signal.</div>
                <div class="pkg-price"><?= $selfie_price > 0 ? '<small>Rs</small>' . number_format($selfie_price, 0) : 'Free' ?></div>
                <ul class="pkg-features">
                    <li>Everything in Identity Verification</li>
                    <li>Live selfie photo or short video, matched to your ID and profile photos</li>
                    <li>“ID + Selfie Verified” badge — the strongest trust signal</li>
                    <li>Documents deleted after review</li>
                </ul>
                <?php if ($loggedIn): ?>
                    <form method="post" action="/verification/start">
                        <?= csrf_field() ?>
                        <input type="hidden" name="tier" value="selfie">
                        <button class="btn btn-gold btn-block">Start Verification</button>
                    </form>
                <?php else: ?>
                    <a href="/register" class="btn btn-gold btn-block">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-card" style="margin-top: 2rem;">
            <h3>How it works</h3>
            <ol style="margin: .5rem 0 0 1.2rem; color: var(--c-ink-soft); line-height: 2;">
                <li><strong>Start</strong> — pick a tier<?= ($identity_price > 0 || $selfie_price > 0) ? ' and complete payment' : '' ?>.</li>
                <li><strong>Submit</strong> — upload your government ID, and for the selfie tier capture a live photo or 5-second video right in your browser.</li>
                <li><strong>Review</strong> — our team compares your ID and selfie against your profile, usually within 24–48 hours.</li>
                <li><strong>Badge</strong> — once approved, the verified badge appears on your profile, in search results and on the homepage.</li>
            </ol>
            <p style="color: var(--c-muted); margin-top: 1rem; font-size: .9rem;">🔒 Privacy: documents are stored outside the public web root, visible only to the review team, and permanently deleted once the review is complete. Your ID is never shown to other members.</p>
        </div>
    <?php endif; ?>
</div>
</section>
