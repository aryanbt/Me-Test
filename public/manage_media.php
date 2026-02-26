<?php
require __DIR__ . '/../app/includes/bootstrap.php';
require __DIR__ . '/../app/includes/db.php';
require __DIR__ . '/../app/includes/auth.php';
require __DIR__ . '/../app/includes/security.php';

require_role([ROLE_ADMIN, ROLE_MANAGER]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();

    if (isset($_POST['upload'])) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $file = $_FILES['media'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            exit('Upload failed.');
        }
        if ($file['size'] > app_config('uploads.max_size')) {
            exit('File too large.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!in_array($mime, app_config('uploads.allowed_mime'), true)) {
            exit('Unsupported media type.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = rtrim(app_config('uploads.path'), '/') . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            exit('Failed to store file.');
        }

        db()->prepare('INSERT INTO media(title,description,tags,file_path,mime_type,media_type,uploaded_by) VALUES(:title,:description,:tags,:file_path,:mime_type,:media_type,:uploaded_by)')
            ->execute([
                'title' => $title,
                'description' => $description,
                'tags' => $tags,
                'file_path' => '/uploads/' . $filename,
                'mime_type' => $mime,
                'media_type' => str_starts_with($mime, 'video/') ? 'video' : 'photo',
                'uploaded_by' => auth_user()['id'],
            ]);
    }

    if (isset($_POST['rename'])) {
        db()->prepare('UPDATE media SET title=:title, description=:description, tags=:tags WHERE id=:id')
            ->execute([
                'id' => (int) $_POST['media_id'],
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'tags' => trim($_POST['tags']),
            ]);
    }

    if (isset($_POST['delete'])) {
        $id = (int) $_POST['media_id'];
        $itemStmt = db()->prepare('SELECT file_path FROM media WHERE id=:id');
        $itemStmt->execute(['id' => $id]);
        if ($item = $itemStmt->fetch()) {
            $local = app_config('uploads.path') . '/' . basename($item['file_path']);
            if (is_file($local)) {
                unlink($local);
            }
        }
        db()->prepare('DELETE FROM media WHERE id=:id')->execute(['id' => $id]);
    }

    if (isset($_POST['bulk_delete'])) {
        $ids = array_map('intval', $_POST['bulk_ids'] ?? []);
        $ids = array_values(array_filter($ids));
        if ($ids) {
            $ph = implode(',', array_fill(0, count($ids), '?'));
            $stmt = db()->prepare("SELECT file_path FROM media WHERE id IN ($ph)");
            $stmt->execute($ids);
            foreach ($stmt->fetchAll() as $item) {
                $local = app_config('uploads.path') . '/' . basename($item['file_path']);
                if (is_file($local)) {
                    unlink($local);
                }
            }
            db()->prepare("DELETE FROM media WHERE id IN ($ph)")->execute($ids);
        }
    }

    redirect('/manage_media.php');
}

$media = db()->query('SELECT id,title,description,tags,file_path,media_type FROM media ORDER BY created_at DESC')->fetchAll();
$title = 'Gallery Management';
require __DIR__ . '/../app/views/header.php';
?>
<section>
    <h1>Gallery Management</h1>
    <form method="post" enctype="multipart/form-data" class="panel">
        <?= csrf_input() ?>
        <input type="hidden" name="upload" value="1">
        <h2>Upload Photo or Video</h2>
        <label>Title<input name="title" required></label>
        <label>Description<textarea name="description" rows="3"></textarea></label>
        <label>Tags<input name="tags"></label>
        <label>File<input type="file" name="media" accept="image/*,video/*" required></label>
        <button class="button" type="submit">Upload</button>
    </form>

    <form method="post" class="panel">
        <?= csrf_input() ?>
        <input type="hidden" name="bulk_delete" value="1">
        <h2>Bulk Delete</h2>
        <div class="checkboxes">
            <?php foreach ($media as $item): ?>
                <label><input type="checkbox" name="bulk_ids[]" value="<?= (int) $item['id'] ?>"> <?= h($item['title']) ?></label>
            <?php endforeach; ?>
        </div>
        <button class="danger" type="submit">Delete Selected</button>
    </form>

    <div class="panel">
        <h2>Edit / Delete Media</h2>
        <div class="media-list">
            <?php foreach ($media as $item): ?>
                <article class="media-row">
                    <?php if ($item['media_type'] === 'video'): ?>
                        <video src="<?= h(media_url($item['file_path'])) ?>" controls preload="metadata"></video>
                    <?php else: ?>
                        <img src="<?= h(media_url($item['file_path'])) ?>" alt="<?= h($item['title']) ?>">
                    <?php endif; ?>
                    <div>
                        <form method="post" class="inline-edit">
                            <?= csrf_input() ?>
                            <input type="hidden" name="rename" value="1">
                            <input type="hidden" name="media_id" value="<?= (int) $item['id'] ?>">
                            <input name="title" value="<?= h($item['title']) ?>" required>
                            <input name="tags" value="<?= h($item['tags']) ?>">
                            <textarea name="description" rows="2"><?= h($item['description']) ?></textarea>
                            <button class="button" type="submit">Save</button>
                        </form>
                        <form method="post">
                            <?= csrf_input() ?>
                            <input type="hidden" name="delete" value="1">
                            <input type="hidden" name="media_id" value="<?= (int) $item['id'] ?>">
                            <button class="danger" type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
