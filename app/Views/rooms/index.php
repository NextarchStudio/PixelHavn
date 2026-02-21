<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Rooms</h3>
<div class="row">
<?php foreach ($rooms as $room): ?>
    <div class="col-md-4 mb-3">
        <div class="card"><div class="card-body">
            <h5><?= esc($room['name']) ?></h5>
            <p><?= esc($room['description']) ?></p>
            <a class="btn btn-primary" href="/rooms/<?= (int) $room['id'] ?>">Join</a>
        </div></div>
    </div>
<?php endforeach; ?>
</div>
<?= $this->endSection() ?>
