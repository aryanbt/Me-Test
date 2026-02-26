<?php
require __DIR__ . '/../app/includes/bootstrap.php';
require __DIR__ . '/../app/includes/db.php';
require __DIR__ . '/../app/includes/auth.php';
require __DIR__ . '/../app/includes/security.php';

require_role([ROLE_ADMIN]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();

    if (isset($_POST['create_user'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = db()->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(:name,:email,:password_hash,:role)');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => $role,
        ]);
    }

    if (isset($_POST['delete_user'])) {
        $id = (int) $_POST['user_id'];
        if ($id !== auth_user()['id']) {
            $stmt = db()->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);
        }
    }

    redirect('/admin_users.php');
}

$users = db()->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();
$title = 'User Management';
require __DIR__ . '/../app/views/header.php';
?>
<section>
    <h1>User Management</h1>
    <form method="post" class="panel">
        <?= csrf_input() ?>
        <input type="hidden" name="create_user" value="1">
        <h2>Create User</h2>
        <label>Name<input name="name" required></label>
        <label>Email<input type="email" name="email" required></label>
        <label>Password<input type="password" name="password" required></label>
        <label>Role
            <select name="role" required>
                <option value="user">User</option>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
            </select>
        </label>
        <button class="button" type="submit">Create</button>
    </form>

    <div class="panel">
        <h2>Existing Users</h2>
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= h($u['name']) ?></td>
                        <td><?= h($u['email']) ?></td>
                        <td><?= h($u['role']) ?></td>
                        <td>
                            <?php if ((int) $u['id'] !== auth_user()['id']): ?>
                                <form method="post">
                                    <?= csrf_input() ?>
                                    <input type="hidden" name="delete_user" value="1">
                                    <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                    <button class="danger" type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
