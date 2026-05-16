<?php

$loginErrors = [];
$registerErrors = [];

function refresh_captcha(): void
{
    $_SESSION['captcha'] = [
        'a' => random_int(2, 9),
        'b' => random_int(2, 9),
    ];
}

if (empty($_SESSION['captcha'])) {
    refresh_captcha();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $mode = $_POST['mode'] ?? '';

    if ($mode === 'login') {
        $login = request_value('login');
        $password = (string) ($_POST['password'] ?? '');

        $stmt = db()->prepare('SELECT * FROM users WHERE login = ? OR email = ?');
        $stmt->execute([$login, $login]);
        $foundUser = $stmt->fetch();

        if (!$foundUser || !password_verify($password, $foundUser['password_hash'])) {
            $loginErrors[] = 'Hibás login név/e-mail vagy jelszó.';
        } else {
            $_SESSION['user_id'] = (int) $foundUser['id'];
            flash('success', 'Sikeres bejelentkezés.');
            redirect_to('fooldal');
        }
    }

    if ($mode === 'register') {
        $familyName = request_value('family_name');
        $givenName = request_value('given_name');
        $login = request_value('reg_login');
        $email = request_value('reg_email');
        $password = (string) ($_POST['reg_password'] ?? '');
        $passwordAgain = (string) ($_POST['reg_password_again'] ?? '');
        $captchaAnswer = (int) ($_POST['captcha_answer'] ?? -1);
        $expectedCaptcha = (int) $_SESSION['captcha']['a'] + (int) $_SESSION['captcha']['b'];

        if (mb_strlen($familyName) < 2) $registerErrors[] = 'A családi név legalább 2 karakter legyen.';
        if (mb_strlen($givenName) < 2) $registerErrors[] = 'Az utónév legalább 2 karakter legyen.';
        if (!preg_match('/^[\p{L}\p{N}_. -]{3,60}$/u', $login)) $registerErrors[] = 'A login név 3-60 karakteres lehet betűkkel, számokkal, szóközzel, ponttal, aláhúzással vagy kötőjellel.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $registerErrors[] = 'Adj meg érvényes e-mail címet.';
        if (strlen($password) < 8) $registerErrors[] = 'A jelszó legalább 8 karakter legyen.';
        if ($password !== $passwordAgain) $registerErrors[] = 'A két jelszó nem egyezik.';
        if ($captchaAnswer !== $expectedCaptcha) $registerErrors[] = 'A captcha eredménye nem helyes.';

        if (!$registerErrors) {
            $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE login = ? OR email = ?');
            $stmt->execute([$login, $email]);
            if ((int) $stmt->fetchColumn() > 0) {
                $registerErrors[] = 'Ez a login név vagy e-mail cím már foglalt.';
            }
        }

        if (!$registerErrors) {
            $stmt = db()->prepare('INSERT INTO users (family_name, given_name, login, email, password_hash) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$familyName, $givenName, $login, $email, password_hash($password, PASSWORD_DEFAULT)]);
            unset($_SESSION['captcha']);
            flash('success', 'Sikeres regisztráció. Most már be tudsz jelentkezni.');
            redirect_to('belepes');
        }

        refresh_captcha();
    }
}
?>

<section class="section split">
    <div class="form-panel">
        <p class="eyebrow">Belépés</p>
        <h1>Visszatérő vendég</h1>
        <?php if ($loginErrors): ?>
            <div class="error-list"><?= h(implode(' ', $loginErrors)) ?></div>
        <?php endif; ?>
        <form method="post" class="form-grid">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="mode" value="login">
            <div class="field full">
                <label for="login">Login név vagy e-mail</label>
                <input id="login" name="login" value="<?= h($_POST['login'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="password">Jelszó</label>
                <input id="password" name="password" type="password">
            </div>
            <div class="form-actions full">
                <button class="primary" type="submit">Belépés</button>
            </div>
        </form>
    </div>

    <div class="form-panel">
        <p class="eyebrow">Regisztráció</p>
        <h1>Új törzsvendég</h1>
        <?php if ($registerErrors): ?>
            <div class="error-list">
                <?php foreach ($registerErrors as $error): ?>
                    <p><?= h($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="form-grid">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="mode" value="register">
            <div class="field">
                <label for="family_name">Családi név</label>
                <input id="family_name" name="family_name" value="<?= h($_POST['family_name'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="given_name">Utónév</label>
                <input id="given_name" name="given_name" value="<?= h($_POST['given_name'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="reg_login">Login név</label>
                <input id="reg_login" name="reg_login" value="<?= h($_POST['reg_login'] ?? '') ?>">
                <small>Használhatsz ékezetes betűket, számokat, szóközt, pontot, aláhúzást vagy kötőjelet.</small>
            </div>
            <div class="field">
                <label for="reg_email">E-mail</label>
                <input id="reg_email" name="reg_email" value="<?= h($_POST['reg_email'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="reg_password">Jelszó</label>
                <input id="reg_password" name="reg_password" type="password">
            </div>
            <div class="field">
                <label for="reg_password_again">Jelszó újra</label>
                <input id="reg_password_again" name="reg_password_again" type="password">
            </div>
            <div class="field full">
                <label for="captcha_answer">Captcha: mennyi <?= h($_SESSION['captcha']['a']) ?> + <?= h($_SESSION['captcha']['b']) ?>?</label>
                <input id="captcha_answer" name="captcha_answer" inputmode="numeric" value="">
            </div>
            <div class="form-actions full">
                <button class="primary" type="submit">Regisztráció</button>
            </div>
        </form>
    </div>
</section>
