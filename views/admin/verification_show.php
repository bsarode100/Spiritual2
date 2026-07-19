<?php /** @var array $req, $photos, $history */
$age = age_from_dob($req['dob'] ?? null);
$hasDocs = !empty($req['id_doc_path']);
$idIsPdf = $hasDocs && str_ends_with($req['id_doc_path'], '.pdf');
$decided = in_array($req['status'], ['approved', 'rejected'], true);
?>
<div class="admin-head">
    <div>
        <span class="eyebrow">Trust Service</span>
        <h1>Review — <?= e($req['user_name']) ?></h1>
    </div>
    <a href="/admin/verification" class="btn btn-ghost btn-sm">← Back to queue</a>
</div>

<div class="admin-card mb-3">
    <h3>Request</h3>
    <div class="info-grid">
        <div>
            <div class="info-row"><span class="k">Member</span><span class="v"><a href="/admin/users/<?= (int)$req['user_id'] ?>"><?= e($req['user_name']) ?></a></span></div>
            <div class="info-row"><span class="k">Email</span><span class="v"><?= e($req['user_email']) ?></span></div>
            <div class="info-row"><span class="k">Phone</span><span class="v"><?= e($req['user_phone'] ?: '—') ?></span></div>
            <div class="info-row"><span class="k">Joined</span><span class="v"><?= e(date('M j, Y', strtotime($req['user_joined']))) ?></span></div>
            <div class="info-row"><span class="k">Profile says</span><span class="v"><?= e(trim(($req['gender'] ? ucfirst($req['gender']) : '') . ($age ? ", $age yrs" : '') . ($req['city'] ? ", {$req['city']}" : ''), ', ')) ?: '—' ?></span></div>
        </div>
        <div>
            <div class="info-row"><span class="k">Tier</span><span class="v"><?= $req['tier'] === 'selfie' ? 'Selfie + Identity' : 'Identity' ?></span></div>
            <div class="info-row"><span class="k">Status</span><span class="v"><span class="pill <?= $req['status'] === 'approved' ? 'green' : ($req['status'] === 'rejected' ? 'red' : 'gold') ?>"><?= e(str_replace('_', ' ', $req['status'])) ?></span></span></div>
            <div class="info-row"><span class="k">Amount</span><span class="v">Rs <?= number_format((float)$req['amount'], 0) ?><?= $req['gateway_payment_id'] ? ' · <code style="font-size:.78rem;">' . e($req['gateway_payment_id']) . '</code>' : '' ?></span></div>
            <div class="info-row"><span class="k">Submitted</span><span class="v"><?= $req['submitted_at'] ? e(date('M j, Y g:i a', strtotime($req['submitted_at']))) : '—' ?></span></div>
            <?php if ($decided): ?>
                <div class="info-row"><span class="k">Reviewed</span><span class="v"><?= e(date('M j, Y g:i a', strtotime($req['reviewed_at']))) ?><?= $req['reviewer_name'] ? ' by ' . e($req['reviewer_name']) : '' ?></span></div>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($req['reject_reason'])): ?>
        <p style="margin-top: .8rem;"><strong>Reason sent to member:</strong> <?= e($req['reject_reason']) ?></p>
    <?php endif; ?>
    <?php if (!empty($req['admin_notes'])): ?>
        <p style="margin-top: .3rem; color: var(--c-muted);"><strong>Internal note:</strong> <?= e($req['admin_notes']) ?></p>
    <?php endif; ?>
</div>

<?php if ($req['status'] === 'pending_review' && $hasDocs): ?>
    <!-- Side-by-side comparison: submitted documents vs the member's profile photos -->
    <div class="info-grid mb-3" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start;">
        <div class="admin-card" style="margin: 0;">
            <h3>Submitted documents</h3>
            <p style="margin: 0 0 .5rem;"><strong>Govt ID:</strong> <?= e(str_replace('_', ' ', ucwords($req['id_doc_type'] ?? 'Not specified', '_'))) ?></p>
            <?php if ($idIsPdf): ?>
                <a href="/admin/verification/<?= (int)$req['id'] ?>/media/id" target="_blank" class="btn btn-primary btn-sm">📄 Open ID document (PDF)</a>
            <?php else: ?>
                <a href="/admin/verification/<?= (int)$req['id'] ?>/media/id" target="_blank">
                    <img src="/admin/verification/<?= (int)$req['id'] ?>/media/id" alt="Government ID" style="width: 100%; border-radius: 8px; border: 1px solid var(--c-line);">
                </a>
            <?php endif; ?>

            <?php if (!empty($req['selfie_path'])): ?>
                <p style="margin: 1rem 0 .5rem;"><strong>Live selfie:</strong> <?= $req['selfie_is_video'] ? '🎥 video' : '📷 photo' ?></p>
                <?php if ($req['selfie_is_video']): ?>
                    <video controls playsinline src="/admin/verification/<?= (int)$req['id'] ?>/media/selfie" style="width: 100%; border-radius: 8px; background: #000;"></video>
                <?php else: ?>
                    <a href="/admin/verification/<?= (int)$req['id'] ?>/media/selfie" target="_blank">
                        <img src="/admin/verification/<?= (int)$req['id'] ?>/media/selfie" alt="Live selfie" style="width: 100%; border-radius: 8px; border: 1px solid var(--c-line);">
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="admin-card" style="margin: 0;">
            <h3>Profile photos (compare)</h3>
            <?php if ($photos): ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: .5rem;">
                    <?php foreach ($photos as $ph): ?>
                        <a href="<?= e(upload_url($ph['path'])) ?>" target="_blank">
                            <img src="<?= e(upload_url($ph['path'])) ?>" alt="" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px; border: <?= $ph['is_primary'] ? '2px solid var(--c-gold)' : '1px solid var(--c-line)' ?>;">
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--c-muted);">Member has no profile photos yet — you may want to reject and ask them to add photos first.</p>
            <?php endif; ?>

            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--c-cream-2);">
                <strong>Review checklist</strong>
                <ul style="margin: .4rem 0 0 1.2rem; color: var(--c-muted); font-size: .9rem; line-height: 1.9;">
                    <li>Name on ID matches the account name (<?= e($req['user_name']) ?>)</li>
                    <li>Photo on ID matches the selfie / profile photos</li>
                    <li>Date of birth is consistent with the profile age<?= $age ? " ($age)" : '' ?></li>
                    <li>ID is not expired, cropped, or visibly edited</li>
                    <?php if ($req['tier'] === 'selfie'): ?><li>Selfie looks live (not a photo of a photo / screen)</li><?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-grid mb-3" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start;">
        <div class="admin-card" style="margin: 0;">
            <h3 style="color: #1F4D1F;">Approve</h3>
            <p style="color: var(--c-muted); font-size: .9rem;">Grants the badge, emails the member, and permanently deletes the submitted documents.</p>
            <form method="post" action="/admin/verification/<?= (int)$req['id'] ?>/approve">
                <?= csrf_field() ?>
                <div class="field">
                    <label>Internal note (optional)</label>
                    <input type="text" name="admin_notes" placeholder="e.g. Aadhaar matched, clear selfie">
                </div>
                <button class="btn btn-primary" style="margin-top: .6rem;">✓ Approve Verification</button>
            </form>
        </div>

        <div class="admin-card" style="margin: 0;">
            <h3 style="color: var(--c-maroon);">Reject</h3>
            <p style="color: var(--c-muted); font-size: .9rem;">The reason is shown to the member so they can fix it and resubmit without paying again. Documents are deleted either way.</p>
            <form method="post" action="/admin/verification/<?= (int)$req['id'] ?>/reject" onsubmit="return confirm('Reject this verification request?')">
                <?= csrf_field() ?>
                <div class="field">
                    <label>Reason shown to member (required)</label>
                    <select name="reject_reason" onchange="document.getElementById('reject-custom').style.display = this.value ? 'none' : 'block'">
                        <option value="">Other — type below…</option>
                        <option value="The ID document is blurry or unreadable — please retake it in good light.">ID blurry / unreadable</option>
                        <option value="The name on the ID does not match your profile name.">Name mismatch</option>
                        <option value="The photo on the ID could not be matched to your selfie or profile photos.">Photo mismatch</option>
                        <option value="The selfie does not appear to be captured live — please use the in-page camera.">Selfie not live</option>
                        <option value="The document appears expired or edited.">Expired / edited document</option>
                    </select>
                    <input type="text" name="reject_reason_custom" id="reject-custom" placeholder="Custom reason shown to the member" style="margin-top: .4rem;">
                </div>
                <div class="field">
                    <label>Internal note (optional)</label>
                    <input type="text" name="admin_notes" placeholder="Admin-only context">
                </div>
                <button class="btn btn-ghost" style="color: var(--c-maroon); margin-top: .6rem;">✗ Reject</button>
            </form>
        </div>
    </div>
<?php elseif (!$decided): ?>
    <div class="admin-card mb-3">
        <p style="color: var(--c-muted); margin: 0;">
            <?= $req['status'] === 'pending_payment' ? 'Waiting for the member to complete payment.' : 'Waiting for the member to submit their documents.' ?>
            Nothing to review yet.
        </p>
    </div>
<?php else: ?>
    <div class="admin-card mb-3">
        <p style="color: var(--c-muted); margin: 0;">This request was decided and the submitted documents were permanently deleted (data-minimisation policy).</p>
    </div>
<?php endif; ?>

<?php if ($history): ?>
<div class="admin-card">
    <h3>Previous requests by this member</h3>
    <table class="tbl">
        <thead><tr><th>Tier</th><th>Status</th><th>Reason</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($history as $h): ?>
            <tr>
                <td><?= $h['tier'] === 'selfie' ? 'Selfie + ID' : 'Identity' ?></td>
                <td><span class="pill <?= $h['status'] === 'approved' ? 'green' : ($h['status'] === 'rejected' ? 'red' : 'gold') ?>"><?= e(str_replace('_', ' ', $h['status'])) ?></span></td>
                <td><?= e($h['reject_reason'] ?: '—') ?></td>
                <td><?= e(date('M j, Y', strtotime($h['created_at']))) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
