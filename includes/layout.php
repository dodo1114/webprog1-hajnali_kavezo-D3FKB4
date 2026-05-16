<?php

$activePage = $_GET['oldal'] ?? 'fooldal';
$title = app_config('app.name');
$user = current_user();
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/app.js" defer></script>
</head>
<body>
<header class="site-header">
    <div class="topbar">
        <a class="brand" href="<?= h(route_url('fooldal')) ?>">
            <span class="brand-mark">HK</span>
            <span>
                <strong>Hajnali Kávézó</strong>
                <small>30% kedvezmény 04:00-06:00</small>
            </span>
        </a>
        <?php if ($user): ?>
            <p class="login-state">Bejelentkezett: <?= h(user_display_name($user)) ?></p>
        <?php endif; ?>
    </div>
    <nav class="main-nav" aria-label="Fő navigáció">
        <?php foreach (nav_items() as $key => $label): ?>
            <a class="<?= $activePage === $key ? 'active' : '' ?>" href="<?= h(route_url($key)) ?>"><?= h($label) ?></a>
        <?php endforeach; ?>
    </nav>
</header>

<main>
    <?php foreach (flashes() as $flash): ?>
        <div class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
    <?php endforeach; ?>

    <?php require $view; ?>
</main>

<footer class="site-footer">
    <p>Hajnali Kávézó - Budapest, Váci utca 1. - A beadandó alkalmazás demonstrációs célú.</p>
</footer>
</body>
</html>

