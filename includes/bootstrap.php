<?php

declare(strict_types=1);

session_start();

const BASE_PATH = __DIR__ . '/..';
const UPLOAD_PATH = BASE_PATH . '/uploads';

$config = require BASE_PATH . '/config/config.php';

function app_config(string $key, mixed $default = null): mixed
{
    global $config;
    $parts = explode('.', $key);
    $value = $config;
    foreach ($parts as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $db = app_config('db');
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset']);
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function route_url(string $page, array $params = []): string
{
    return '?' . http_build_query(array_merge(['oldal' => $page], $params));
}

function redirect_to(string $page, array $params = []): never
{
    header('Location: ' . route_url($page, $params));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(400);
        exit('Érvénytelen űrlapküldés.');
    }
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user === null) {
        $stmt = db()->prepare('SELECT id, family_name, given_name, login, email FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: false;
    }

    return $user ?: null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('warning', 'Ehhez az oldalhoz be kell jelentkezni.');
        redirect_to('belepes');
    }
}

function user_display_name(array $user): string
{
    return $user['family_name'] . ' ' . $user['given_name'] . ' (' . $user['login'] . ')';
}

function nav_items(): array
{
    $items = [
        'fooldal' => 'Főoldal',
        'kepek' => 'Képek',
        'kapcsolat' => 'Kapcsolat',
        'crud' => 'CRUD',
    ];

    if (is_logged_in()) {
        $items['uzenetek'] = 'Üzenetek';
        $items['kilepes'] = 'Kilépés';
    } else {
        $items['belepes'] = 'Bejelentkezés';
    }

    return $items;
}

function request_value(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

