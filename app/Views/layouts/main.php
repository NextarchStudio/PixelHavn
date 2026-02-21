<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($appTitle ?? 'PixelHavn') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/dashboard">PixelHavn</a>
        <?php if (! empty($currentUser)): ?>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-light" href="/rooms">Rooms</a>
                <a class="btn btn-sm btn-outline-light" href="/inventory">Inventory</a>
                <a class="btn btn-sm btn-outline-light" href="/passkeys">Passkeys</a>
                <a class="btn btn-sm btn-outline-light" href="/admin">Admin</a>
                <a class="btn btn-sm btn-warning" href="/logout">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container pb-5">
    <?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>
    <?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
    <?= $this->renderSection('content') ?>
</div>
</body>
</html>
