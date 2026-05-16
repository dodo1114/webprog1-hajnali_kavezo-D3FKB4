<?php

require_login();

$stmt = db()->query(
    "SELECT messages.*, users.family_name, users.given_name, users.login
     FROM messages
     LEFT JOIN users ON users.id = messages.user_id
     ORDER BY messages.created_at DESC"
);
$messages = $stmt->fetchAll();
?>

<section class="section">
    <p class="eyebrow">Üzenetek</p>
    <h1>Beérkezett kapcsolatfelvételek</h1>
    <div class="table-panel">
        <table>
            <thead>
            <tr>
                <th>Idő</th>
                <th>Küldő</th>
                <th>E-mail</th>
                <th>Tárgy</th>
                <th>Üzenet</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?= h($message['created_at']) ?></td>
                    <td>
                        <?php if ($message['login']): ?>
                            <?= h($message['family_name'] . ' ' . $message['given_name'] . ' (' . $message['login'] . ')') ?>
                        <?php else: ?>
                            Vendég
                        <?php endif; ?>
                    </td>
                    <td><?= h($message['email']) ?></td>
                    <td><?= h($message['subject']) ?></td>
                    <td><?= nl2br(h($message['message'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

