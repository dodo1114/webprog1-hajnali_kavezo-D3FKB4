<?php

$errors = [];
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = request_value('name');
    $email = request_value('email');
    $subject = request_value('subject');
    $message = request_value('message');

    if (mb_strlen($name) < 2) $errors[] = 'A név legalább 2 karakter legyen.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Adj meg érvényes e-mail címet.';
    if (mb_strlen($subject) < 4) $errors[] = 'A tárgy legalább 4 karakter legyen.';
    if (mb_strlen($message) < 10) $errors[] = 'Az üzenet legalább 10 karakter legyen.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$user['id'] ?? null, $name, $email, $subject, $message]);
        redirect_to('kapcsolat-eredmeny', ['id' => db()->lastInsertId()]);
    }
}
?>

<section class="section compact">
    <p class="eyebrow">Kapcsolat</p>
    <h1>Írj a kávézónak</h1>
    <div class="form-panel">
        <?php if ($errors): ?>
            <div class="error-list">
                <?php foreach ($errors as $error): ?>
                    <p><?= h($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="form-grid" data-contact-form>
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <div class="form-errors field full"></div>
            <div class="field">
                <label for="name">Név</label>
                <input id="name" name="name" value="<?= h($_POST['name'] ?? ($user ? $user['family_name'] . ' ' . $user['given_name'] : '')) ?>">
            </div>
            <div class="field">
                <label for="email">E-mail</label>
                <input id="email" name="email" value="<?= h($_POST['email'] ?? ($user['email'] ?? '')) ?>">
            </div>
            <div class="field full">
                <label for="subject">Tárgy</label>
                <input id="subject" name="subject" value="<?= h($_POST['subject'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="message">Üzenet</label>
                <textarea id="message" name="message"><?= h($_POST['message'] ?? '') ?></textarea>
            </div>
            <div class="form-actions full">
                <button class="primary" type="submit">Üzenet küldése</button>
            </div>
        </form>
    </div>
</section>

