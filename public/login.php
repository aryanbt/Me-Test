<?php
require __DIR__ . '/../app/includes/bootstrap.php';
require __DIR__ . '/../app/includes/db.php';
require __DIR__ . '/../app/includes/auth.php';
require __DIR__ . '/../app/includes/security.php';

if (is_logged_in()) {
    redirect('/dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf()) {
        $error = 'Invalid request token.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (attempt_login($email, $password)) {
            redirect('/dashboard.php');
        }
        $error = 'Invalid credentials.';
    }
}

$title = 'Login';
require __DIR__ . '/../app/views/header.php';
?>
<section class="auth-box">
    <h1>Login</h1>
    <p>Sign in to access your dashboard. Signup is disabled.</p>
    <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
    <form method="post">
        <?= csrf_input() ?>
        <label>Email<input name="email" type="email" required></label>
        <label>Password<input name="password" type="password" required></label>
        <button class="button" type="submit">Login</button>
    </form>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
