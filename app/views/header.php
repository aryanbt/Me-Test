<?php
$user = auth_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? app_config('app_name')) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="site-header">
    <div class="container topbar">
        <a class="brand" href="/">📸 <?= h(app_config('app_name')) ?></a>
        <nav>
            <a href="/gallery.php">Gallery</a>
            <?php if ($user): ?>
                <a href="/dashboard.php">Dashboard</a>
                <?php if ($user['role'] === ROLE_ADMIN): ?><a href="/admin_users.php">Users</a><?php endif; ?>
                <a href="/logout.php">Logout</a>
            <?php else: ?>
                <a href="/login.php">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
