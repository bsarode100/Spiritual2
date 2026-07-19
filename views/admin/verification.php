<?php /** @var array $rows, $stats, $prices; @var string $filter */
$statusPill = fn(string $s): string => match ($s) {
    'approved' => 'green',
    'rejected' => 'red',
    default    => 'gold',
};
$statusLabel = fn(string $s): string => match ($s) {
    'pending_payment' => 'Awaiting payment',
    'pending_upload'  => 'Awaiting documents',
    'pending_review'  => 'Ready for review',
    default           => ucfirst($s),
};
?>
<div class="admin-head">
    <div><span class="eyebrow">Trust Service</span><h1>Verification Requests</h1></div>
    <a href="/verification" target="_blank" class="btn btn-ghost btn-sm">View Public Page</a>
</div>

<div class="stat-cards mb-4">
    <div class="stat-card"><div class="label">Pending Review</div><div class="value"><?= number_format((int)$stats['pending']) ?></div></div>
    <div class="stat-card"><div class="label">Awaiting Member</div><div class="value"><?= number_format((int)$stats['awaiting']) ?></div></div>
    <div class="stat-card"><div class="label">Approved</div><div class="value"><?= number_format((int)$stats['approved']) ?></div></div>
    <div class="stat-card"><div class="label">Rejected</div><div class="value"><?= number_format((int)$stats['rejected']) ?></div></div>
    <div class="stat-card"><div class="label">Revenue</div><div class="value">Rs <?= number_format((float)$stats['revenue'], 0) ?></div></div>
</div>

<div class="admin-card mb-4">
    <h3>Verification Pricing</h3>
    <p style="color: var(--c-muted); font-size: .9rem;">Set a price to 0 to make that tier free — members then skip payment and go straight to document submission.</p>
    <form method="post" action="/admin/verification/pricing" class="form-grid-3">
        <?= csrf_field() ?>
        <div class="field">
            <label>Identity Verification</label>
            <input type="number" name="verify_identity_price" value="<?= e($prices['identity']) ?>" min="0">
        </div>
        <div class="field">
            <label>Selfie + Identity</label>
            <input type="number" name="verify_selfie_price" value="<?= e($prices['selfie']) ?>" min="0">
        </div>
        <div class="field" style="justify-content: end;">
            <button class="btn btn-primary">Save Pricing</button>
        </div>
    </form>
</div>

<div class="admin-card">
    <div class="flex-between" style="flex-wrap: wrap; gap: .5rem;">
        <h3>Requests</h3>
        <div class="flex gap-1" style="flex-wrap: wrap;">
            <?php foreach (['' => 'All', 'pending_review' => 'To review', 'pending_upload' => 'Awaiting docs', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $label): ?>
                <a href="/admin/verification<?= $k ? '?status=' . $k : '' ?>"
                   class="btn btn-sm <?= ($filter ?? '') === $k ? 'btn-primary' : 'btn-ghost' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if (!$rows): ?>
        <p style="color: var(--c-muted);">No verification requests<?= !empty($filter) ? ' with this status' : ' yet' ?>.</p>
    <?php else: ?>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Tier</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Payment</th>
                    <th>Requested</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td>
                        <strong><a href="/admin/users/<?= (int)$r['user_id'] ?>"><?= e($r['user_name']) ?></a></strong>
                        <br><span style="color: var(--c-muted); font-size: .82rem;"><?= e($r['user_email']) ?></span>
                    </td>
                    <td>
                        <?= $r['tier'] === 'selfie' ? 'Selfie + ID' : 'Identity' ?>
                        <?php if (!empty($r['current_verified_tier']) && $r['current_verified_tier'] !== 'none'): ?>
                            <br><span class="pill green"><?= e($r['current_verified_tier']) ?> verified</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="pill <?= $statusPill($r['status']) ?>"><?= e($statusLabel($r['status'])) ?></span></td>
                    <td>
                        <?php if (!empty($r['id_doc_path'])): ?>
                            <?= e(str_replace('_', ' ', ucfirst($r['id_doc_type'] ?? 'ID'))) ?>
                            <?php if (!empty($r['selfie_path'])): ?><br><?= $r['selfie_is_video'] ? '🎥 video selfie' : '📷 photo selfie' ?><?php endif; ?>
                            <?php if (!empty($r['submitted_at'])): ?><br><span style="color: var(--c-muted); font-size: .8rem;"><?= e(date('M j, g:i a', strtotime($r['submitted_at']))) ?></span><?php endif; ?>
                        <?php elseif (in_array($r['status'], ['approved','rejected'], true)): ?>
                            <span style="color: var(--c-muted); font-size: .82rem;">Purged after review</span>
                        <?php else: ?>
                            <span style="color: var(--c-muted); font-size: .82rem;">Not submitted</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        Rs <?= number_format((float)$r['amount'], 0) ?>
                        <?php if (!empty($r['gateway_payment_id'])): ?>
                            <br><code style="font-size: .78rem;"><?= e($r['gateway_payment_id']) ?></code>
                        <?php elseif ($r['status'] === 'pending_payment'): ?>
                            <br><span style="color: var(--c-muted); font-size: .82rem;">Awaiting payment</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td>
                    <td>
                        <a href="/admin/verification/<?= (int)$r['id'] ?>" class="btn btn-sm <?= $r['status'] === 'pending_review' ? 'btn-primary' : 'btn-ghost' ?>">
                            <?= $r['status'] === 'pending_review' ? 'Review' : 'View' ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
