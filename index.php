<?php

require __DIR__ . '/includes/bootstrap.php';

$routes = [
    'fooldal' => 'home.php',
    'kepek' => 'gallery.php',
    'kapcsolat' => 'contact.php',
    'kapcsolat-eredmeny' => 'contact_result.php',
    'uzenetek' => 'messages.php',
    'crud' => 'crud.php',
    'belepes' => 'login.php',
    'kilepes' => 'logout.php',
];

$page = $_GET['oldal'] ?? 'fooldal';
if (!array_key_exists($page, $routes)) {
    http_response_code(404);
    $page = '404';
}

$view = $page === '404' ? __DIR__ . '/pages/404.php' : __DIR__ . '/pages/' . $routes[$page];
require __DIR__ . '/includes/layout.php';

