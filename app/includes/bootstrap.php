<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config/config.php';

session_name($config['security']['session_name']);
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'cookie_samesite' => 'Lax',
    ]);
}

date_default_timezone_set('UTC');

function app_base_path(): string
{
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $configured = trim((string) getenv('APP_BASE_PATH'));
    if ($configured !== '') {
        $basePath = '/' . trim($configured, '/');
        return $basePath === '/' ? '' : $basePath;
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($scriptName));
    $dir = rtrim($dir, '/');

    $basePath = ($dir === '' || $dir === '.') ? '' : $dir;
    return $basePath;
}

function app_config(?string $key = null, mixed $default = null): mixed
{
    global $config;

    if ($key === null) {
        return $config;
    }

    $segments = explode('.', $key);
    $value = $config;

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return app_base_path() . $path;
}

function media_url(string $path): string
{
    if (preg_match('/^https?:\/\//i', $path) === 1) {
        return $path;
    }

    if (app_base_path() !== '' && str_starts_with($path, app_base_path() . '/')) {
        return $path;
    }

    return url($path);
}

function view(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../views/' . $name . '.php';
}

function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
