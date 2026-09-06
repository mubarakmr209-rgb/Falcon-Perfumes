<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function start_app_session(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    start_app_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): bool
{
    $submitted = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';

    return is_string($submitted) && is_string($stored) && hash_equals($stored, $submitted);
}

function set_flash(string $type, string $message): void
{
    start_app_session();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** @return array{type: string, message: string}|null */
function get_flash(): ?array
{
    start_app_session();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return is_array($flash) ? $flash : null;
}

function is_logged_in(): bool
{
    start_app_session();
    return isset($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to view your dashboard.');
        redirect('auth/login.php');
    }
}

function current_user_name(): string
{
    start_app_session();
    return (string) ($_SESSION['username'] ?? 'Customer');
}

/** @return array<string, string> */
function validate_registration(string $username, string $email, string $password): array
{
    $errors = [];

    if (mb_strlen($username) < 3 || mb_strlen($username) > 80) {
        $errors['username'] = 'Username must be between 3 and 80 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors['password'] = 'Password must contain at least 8 characters.';
    }

    return $errors;
}
