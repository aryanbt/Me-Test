<?php

declare(strict_types=1);

return [
    'app_name' => 'Photo Gallery CMS',
    'base_url' => getenv('APP_BASE_URL') ?: 'http://localhost',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'gallery_cms',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'security' => [
        'session_name' => 'gallery_cms_session',
        'csrf_key' => '_csrf',
    ],
    'uploads' => [
        'path' => __DIR__ . '/../../public/uploads',
        'public_prefix' => '/uploads/',
        'max_size' => 50 * 1024 * 1024,
        'allowed_mime' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'video/mp4',
            'video/webm',
            'video/quicktime',
        ],
    ],
];
