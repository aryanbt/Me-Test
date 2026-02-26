<?php
$user = auth_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? app_config('app_name')) ?></title>
    <link rel="stylesheet" href="<?= h(url('/assets/css/app.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="container topbar">
        <a class="brand" href="<?= h(url('/')) ?>">📸 <?= h(app_config('app_name')) ?></a>
        <nav>
            <a href="<?= h(url('/gallery.php')) ?>">Gallery</a>
            <?php if ($user): ?>
                <a href="<?= h(url('/dashboard.php')) ?>">Dashboard</a>
                <?php if ($user['role'] === ROLE_ADMIN): ?><a href="<?= h(url('/admin_users.php')) ?>">Users</a><?php endif; ?>
                <a href="<?= h(url('/logout.php')) ?>">Logout</a>
            <?php else: ?>
                <a href="<?= h(url('/login.php')) ?>">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
