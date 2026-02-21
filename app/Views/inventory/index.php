<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Inventory</h3>
<table class="table table-striped bg-white">
    <thead><tr><th>Item</th><th>Type</th><th>Qty</th><th>Place</th></tr></thead>
    <tbody>
    <?php foreach ($inventory as $item): ?>
    <tr>
        <td><?= esc($item['name']) ?></td>
        <td><?= esc($item['type']) ?></td>
        <td><?= (int) $item['quantity'] ?></td>
        <td>
            <form method="post" action="/inventory/place" class="d-flex gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="item_id" value="<?= (int) $item['item_id'] ?>">
                <input type="number" name="room_id" class="form-control" placeholder="Room ID" required>
                <input type="number" name="x" class="form-control" placeholder="X" required>
                <input type="number" name="y" class="form-control" placeholder="Y" required>
                <input type="number" name="rotation" class="form-control" value="0">
                <button class="btn btn-sm btn-primary">Place</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
