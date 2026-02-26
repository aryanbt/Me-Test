<?php

declare(strict_types=1);

function csrf_token(): string
{
    $key = app_config('security.csrf_key');
    if (empty($_SESSION[$key])) {
        $_SESSION[$key] = bin2hex(random_bytes(32));
    }

    return $_SESSION[$key];
}

function csrf_input(): string
{
    return '<input type="hidden" name="_token" value="' . h(csrf_token()) . '">';
}

function validate_csrf(): bool
{
    $key = app_config('security.csrf_key');
    return hash_equals($_SESSION[$key] ?? '', $_POST['_token'] ?? '');
}

function require_post_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf()) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}
