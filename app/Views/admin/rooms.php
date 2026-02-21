<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Rooms</h3>
<form method="post" action="/admin/rooms" class="card card-body mb-3">
    <?= csrf_field() ?>
    <div class="row g-2">
        <div class="col-md-4"><input class="form-control" name="name" placeholder="Room name" required></div>
        <div class="col-md-5"><input class="form-control" name="description" placeholder="Description"></div>
        <div class="col-md-2 form-check mt-2"><input class="form-check-input" type="checkbox" name="is_public" checked> Public</div>
        <div class="col-md-1"><button class="btn btn-primary">Create</button></div>
    </div>
</form>
<ul class="list-group bg-white">
<?php foreach ($rooms as $room): ?>
    <li class="list-group-item">#<?= (int) $room['id'] ?> - <?= esc($room['name']) ?></li>
<?php endforeach; ?>
</ul>
<?= $this->endSection() ?>
