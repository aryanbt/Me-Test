<?php
require __DIR__ . '/../app/includes/bootstrap.php';
require __DIR__ . '/../app/includes/db.php';
require __DIR__ . '/../app/includes/auth.php';

$media = [];
$dbError = null;
try {
    $stmt = db()->query("SELECT id, title, description, file_path, media_type FROM media WHERE status='published' ORDER BY created_at DESC LIMIT 12");
    $media = $stmt->fetchAll();
} catch (Throwable $e) {
    $dbError = 'Gallery is temporarily unavailable until database connection is configured.';
}

$title = 'Home';
require __DIR__ . '/../app/views/header.php';
?>
<section class="hero">
    <h1>Modern Photo & Video Gallery CMS</h1>
    <p>Discover beautifully organized visual stories. Scroll to load more media seamlessly.</p>
    <a class="button" href="/gallery.php">Open Full Gallery</a>
</section>
<?php if ($dbError): ?><p class="error"><?= h($dbError) ?></p><?php endif; ?>
<section class="grid" id="gallery-grid">
    <?php foreach ($media as $item): ?>
        <article class="card">
            <?php if ($item['media_type'] === 'video'): ?>
                <video controls preload="metadata" src="<?= h($item['file_path']) ?>"></video>
            <?php else: ?>
                <img loading="lazy" src="<?= h($item['file_path']) ?>" alt="<?= h($item['title']) ?>">
            <?php endif; ?>
            <div class="card-body">
                <h3><?= h($item['title']) ?></h3>
                <p><?= h($item['description']) ?></p>
            </div>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
