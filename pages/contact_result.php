<?php

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM messages WHERE id = ?');
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    http_response_code(404);
}
?>

<section class="section compact">
    <p class="eyebrow">Elküldött üzenet</p>
    <h1><?= $message ? 'Köszönjük az üzenetet' : 'Az üzenet nem található' ?></h1>
    <?php if ($message): ?>
        <div class="card">
            <p><strong>Név:</strong> <?= h($message['name']) ?></p>
            <p><strong>E-mail:</strong> <?= h($message['email']) ?></p>
            <p><strong>Tárgy:</strong> <?= h($message['subject']) ?></p>
            <p><strong>Üzenet:</strong><br><?= nl2br(h($message['message'])) ?></p>
            <p><strong>Küldés ideje:</strong> <?= h($message['created_at']) ?></p>
        </div>
    <?php endif; ?>
</section>

