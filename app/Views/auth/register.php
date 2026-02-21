<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-3">Register</h3>
                <form method="post" action="/register">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
                    <button class="btn btn-primary">Create account</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
