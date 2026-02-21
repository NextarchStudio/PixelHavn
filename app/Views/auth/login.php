<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-3">Login</h3>
                <form method="post" action="/login">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Username or Email</label>
                        <input class="form-control" name="identity" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <button class="btn btn-primary">Login</button>
                    <a class="btn btn-link" href="/register">Register</a>
                    <a class="btn btn-link" href="/auth/passkey-login">Passkey Login</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
