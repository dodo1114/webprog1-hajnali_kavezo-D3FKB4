<?php

$uploadErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    verify_csrf();

    $title = request_value('title');
    $file = $_FILES['image'] ?? null;

    if (mb_strlen($title) < 3) {
        $uploadErrors[] = 'A kép címe legalább 3 karakter legyen.';
    }
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors[] = 'Válassz ki egy feltölthető képet.';
    }

    if (!$uploadErrors) {
        $info = getimagesize($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        if (!$info || !isset($allowed[$info['mime']])) {
            $uploadErrors[] = 'Csak JPG, PNG, WEBP vagy GIF kép tölthető fel.';
        } elseif ($file['size'] > 3 * 1024 * 1024) {
            $uploadErrors[] = 'A kép mérete legfeljebb 3 MB lehet.';
        } else {
            if (!is_dir(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH, 0775, true);
            }
            $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$info['mime']];
            $target = UPLOAD_PATH . '/' . $filename;
            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $uploadErrors[] = 'A kép mentése nem sikerült.';
            } else {
                $stmt = db()->prepare('INSERT INTO images (user_id, title, filename) VALUES (?, ?, ?)');
                $stmt->execute([current_user()['id'], $title, $filename]);
                flash('success', 'A kép feltöltése sikerült.');
                redirect_to('kepek');
            }
        }
    }
}

$stmt = db()->query('SELECT images.*, users.login FROM images LEFT JOIN users ON users.id = images.user_id ORDER BY images.created_at DESC');
$uploadedImages = $stmt->fetchAll();

$sampleImages = [
    ['title' => 'Hajnali espresso', 'url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=900&q=80'],
    ['title' => 'Friss pult', 'url' => 'https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=900&q=80'],
    ['title' => 'Csendes reggel', 'url' => 'https://images.unsplash.com/photo-1498804103079-a6351b050096?auto=format&fit=crop&w=900&q=80'],
];
?>

<section class="section">
    <p class="eyebrow">Képek</p>
    <h1>Galéria</h1>
    <div class="card-grid">
        <?php foreach ($sampleImages as $image): ?>
            <article class="card">
                <img class="gallery-img" src="<?= h($image['url']) ?>" alt="<?= h($image['title']) ?>">
                <h2><?= h($image['title']) ?></h2>
            </article>
        <?php endforeach; ?>
        <?php foreach ($uploadedImages as $image): ?>
            <article class="card">
                <img class="gallery-img" src="uploads/<?= h($image['filename']) ?>" alt="<?= h($image['title']) ?>">
                <h2><?= h($image['title']) ?></h2>
                <p>Feltöltötte: <?= h($image['login'] ?? 'ismeretlen') ?>, <?= h($image['created_at']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section compact">
    <div class="form-panel">
        <p class="eyebrow">Feltöltés</p>
        <h2>Új kép</h2>
        <?php if (!is_logged_in()): ?>
            <p>Képfeltöltést csak bejelentkezett felhasználó végezhet.</p>
            <a class="button primary" href="<?= h(route_url('belepes')) ?>">Bejelentkezés</a>
        <?php else: ?>
            <?php if ($uploadErrors): ?>
                <div class="error-list">
                    <?php foreach ($uploadErrors as $error): ?>
                        <p><?= h($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                <div class="field full">
                    <label for="title">Kép címe</label>
                    <input id="title" name="title" value="<?= h($_POST['title'] ?? '') ?>">
                </div>
                <div class="field full">
                    <label for="image">Képfájl</label>
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
                </div>
                <div class="form-actions full">
                    <button class="primary" type="submit">Feltöltés</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

