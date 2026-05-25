<?php /** @var array $u */ ?>
<div class="admin-head"><h1>My Profile</h1></div>
<form method="post" action="/admin/profile" class="admin-card">
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field"><label>Name</label><input type="text" name="name" value="<?= e($u['name']) ?>"></div>
        <div class="field"><label>Email</label><input type="email" name="email" value="<?= e($u['email']) ?>"></div>
    </div>
    <h3 class="mt-3">Change Password</h3>
    <div class="form-grid">
        <div class="field"><label>Current password</label><input type="password" name="current_password"></div>
        <div class="field"><label>New password</label><input type="password" name="new_password" minlength="6"></div>
    </div>
    <button class="btn btn-primary">Save Changes</button>
</form>
