<?php

declare(strict_types=1);

const ROLE_ADMIN = 'admin';
const ROLE_MANAGER = 'manager';
const ROLE_USER = 'user';

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return auth_user() !== null;
}

function attempt_login(string $email, string $password): bool
{
    $stmt = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    session_regenerate_id(true);

    return true;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}

function require_auth(): void
{
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

function require_role(array $roles): void
{
    require_auth();
    $role = auth_user()['role'];
    if (!in_array($role, $roles, true)) {
        http_response_code(403);
        exit('Forbidden.');
    }
}
