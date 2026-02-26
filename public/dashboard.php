<?php
require __DIR__ . '/../app/includes/bootstrap.php';
require __DIR__ . '/../app/includes/db.php';
require __DIR__ . '/../app/includes/auth.php';
require_auth();

$user = auth_user();
$title = 'Dashboard';
require __DIR__ . '/../app/views/header.php';
?>
<section>
    <h1>Welcome, <?= h($user['name']) ?></h1>
    <p>Role: <strong><?= h($user['role']) ?></strong></p>
    <div class="actions">
        <?php if (in_array($user['role'], [ROLE_ADMIN, ROLE_MANAGER], true)): ?>
            <a class="button" href="<?= h(url('/manage_media.php')) ?>">Manage Media</a>
        <?php endif; ?>
        <?php if ($user['role'] === ROLE_ADMIN): ?>
            <a class="button secondary" href="<?= h(url('/admin_users.php')) ?>">Manage Users</a>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
