<?php
require __DIR__ . '/../app/includes/bootstrap.php';
require __DIR__ . '/../app/includes/db.php';
require __DIR__ . '/../app/includes/auth.php';
$title = 'Gallery';
require __DIR__ . '/../app/views/header.php';
?>
<section>
    <h1>Full Gallery</h1>
    <p>Infinite scroll enabled. New images and videos load automatically.</p>
</section>
<section class="grid" id="gallery-grid" data-infinite="1" data-api-url="<?= h(url('/api/media.php')) ?>"></section>
<div id="loading" class="loading">Loading more media…</div>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
