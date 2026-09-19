<?php $u = Auth::user(); ?>
<section class="section-tight"><div class="container-sm">
    <div class="flex-between mb-4">
        <h1 style="margin: 0;">Settings</h1>
        <a href="/dashboard" class="btn btn-ghost btn-sm">← Dashboard</a>
    </div>

    <form method="post" action="/settings" class="admin-card">
        <?= csrf_field() ?>
        <h3>Account</h3>
        <div class="field"><label>Name</label><input type="text" name="name" value="<?= e($u['name']) ?>"></div>
        <div class="field"><label>Email</label><input type="email" value="<?= e($u['email']) ?>" disabled></div>
        <div class="field"><label>Phone</label><input type="tel" name="phone" value="<?= e($u['phone']) ?>"></div>

        <h3 class="mt-4">Change Password</h3>
        <div class="field"><label>Current password</label><input type="password" name="current_password" id="current_password"></div>
        <div class="field">
            <label>New password</label>
            <input type="password" name="new_password" id="new_password" minlength="6">
            <label class="show-password-label">
                <input type="checkbox" class="show-password-checkbox" data-targets="current_password,new_password">
                <span>Show passwords</span>
            </label>
        </div>
        <span class="field-help">Leave blank to keep your current password.</span>

        <button class="btn btn-primary mt-3">Save Changes</button>
    </form>
</div></section>
