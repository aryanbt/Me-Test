<?php
require __DIR__ . '/../../app/includes/bootstrap.php';
require __DIR__ . '/../../app/includes/db.php';

header('Content-Type: application/json');

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = min(24, max(1, (int) ($_GET['limit'] ?? 12)));
$offset = ($page - 1) * $limit;

$stmt = db()->prepare("SELECT id, title, description, file_path, media_type FROM media WHERE status='published' ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll();
echo json_encode([
    'data' => $items,
    'next_page' => count($items) === $limit ? $page + 1 : null,
], JSON_THROW_ON_ERROR);
