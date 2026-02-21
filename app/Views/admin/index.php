<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Admin Panel</h3>
<form method="post" action="/admin/settings" class="card card-body mb-3">
    <?= csrf_field() ?>
    <?php
    $settingsMap = [];
    foreach ($settings as $setting) {
        $settingsMap[$setting['key']] = $setting['value'];
    }
    ?>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="auth_password_enabled" id="authPassword" <?= ($settingsMap['auth_password_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
        <label class="form-check-label" for="authPassword">Enable password login</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="auth_passkey_enabled" id="authPasskey" <?= ($settingsMap['auth_passkey_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
        <label class="form-check-label" for="authPasskey">Enable passkey auth</label>
    </div>
    <button class="btn btn-primary mt-2">Save settings</button>
</form>
<div class="d-flex gap-2">
    <a class="btn btn-outline-primary" href="/admin/users">Manage Users</a>
    <a class="btn btn-outline-primary" href="/admin/rooms">Manage Rooms</a>
    <a class="btn btn-outline-primary" href="/admin/items">Manage Items</a>
</div>
<?= $this->endSection() ?>
