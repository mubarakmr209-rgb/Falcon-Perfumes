<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
start_app_session();

// A logout link is intentionally safe to use: it clears only this app's session.
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
}
session_destroy();

session_start();
set_flash('success', 'You have been logged out safely.');
redirect('login.php');
