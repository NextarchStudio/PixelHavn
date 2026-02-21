<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Users</h3>
<table class="table table-bordered bg-white">
<thead><tr><th>ID</th><th>User</th><th>Email</th><th>Banned</th><th>Assign Role</th><th>Moderate</th></tr></thead>
<tbody>
<?php foreach ($users as $u): ?>
<tr>
    <td><?= (int) $u['id'] ?></td>
    <td><?= esc($u['username']) ?></td>
    <td><?= esc($u['email']) ?></td>
    <td><?= (int) $u['is_banned'] ? 'Yes' : 'No' ?></td>
    <td>
        <form method="post" action="/admin/users/<?= (int) $u['id'] ?>/role" class="d-flex gap-2">
            <?= csrf_field() ?>
            <select name="role_name" class="form-select form-select-sm">
            <?php foreach ($roles as $r): ?>
                <option value="<?= esc($r['name']) ?>"><?= esc($r['name']) ?></option>
            <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-primary">Assign</button>
        </form>
    </td>
    <td>
        <form method="post" action="/admin/users/<?= (int) $u['id'] ?>/ban">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-warning"><?= (int) $u['is_banned'] ? 'Unban' : 'Ban' ?></button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?= $this->endSection() ?>
