<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2>Welcome, <?= esc($currentUser['username'] ?? '') ?></h2>
<p class="text-muted">Roles: <?= esc(implode(', ', $currentUser['roles'] ?? [])) ?></p>
<div class="row">
    <div class="col-md-6">
        <div class="card"><div class="card-body">
            <h5>Public Rooms</h5>
            <ul class="list-group">
                <?php foreach ($rooms as $room): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= esc($room['name']) ?>
                        <a class="btn btn-sm btn-primary" href="/rooms/<?= (int) $room['id'] ?>">Enter</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>
