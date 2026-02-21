<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card"><div class="card-body">
<h3>Change Password</h3>
<form method="post" action="/auth/change-password">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">New Password</label><input type="password" class="form-control" name="password" required></div>
    <div class="mb-3"><label class="form-label">Confirm</label><input type="password" class="form-control" name="password_confirm" required></div>
    <button class="btn btn-primary">Update Password</button>
</form>
</div></div></div></div>
<?= $this->endSection() ?>
