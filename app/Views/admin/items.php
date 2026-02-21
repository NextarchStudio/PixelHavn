<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Items</h3>
<form method="post" action="/admin/items" class="card card-body mb-3">
    <?= csrf_field() ?>
    <div class="row g-2">
        <div class="col-md-2"><input class="form-control" name="item_key" placeholder="item_key" required></div>
        <div class="col-md-3"><input class="form-control" name="name" placeholder="Name" required></div>
        <div class="col-md-2"><input class="form-control" name="type" placeholder="Type" required></div>
        <div class="col-md-3"><input class="form-control" name="meta_json" placeholder='{"rarity":"common"}'></div>
        <div class="col-md-1 form-check mt-2"><input class="form-check-input" type="checkbox" name="is_tradeable" checked> Trade</div>
        <div class="col-md-1"><button class="btn btn-primary">Create</button></div>
    </div>
</form>
<table class="table table-striped bg-white"><thead><tr><th>ID</th><th>Key</th><th>Name</th><th>Type</th></tr></thead><tbody>
<?php foreach ($items as $item): ?>
<tr><td><?= (int) $item['id'] ?></td><td><?= esc($item['item_key']) ?></td><td><?= esc($item['name']) ?></td><td><?= esc($item['type']) ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<?= $this->endSection() ?>
