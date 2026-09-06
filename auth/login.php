<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
start_app_session();

if (is_logged_in()) {
    redirect('../dashboard.php');
}

$error = '';
$email = '';
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (!verify_csrf()) {
        $error = 'Your form session expired. Please try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        try {
            $statement = db()->prepare(
                'SELECT id, username, password FROM users WHERE email = :email LIMIT 1'
            );
            $statement->execute(['email' => $email]);
            $user = $statement->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['username'] = $user['username'];
                redirect('../dashboard.php');
            }

            $error = 'Invalid email address or password.';
        } catch (PDOException $exception) {
            error_log('Login failed: ' . $exception->getMessage());
            $error = 'We could not log you in right now. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Falcon Perfumes</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-shell">
  <div class="auth-side"><div class="text-center position-relative"><div class="fp-brand justify-content-center" style="font-size:2rem;"><span>FALCON<small>PERFUMES</small></span></div><p class="text-muted-fp mt-3" style="max-width:340px;">Your account keeps your scent discoveries in one place.</p></div></div>
  <div class="auth-form-side"><div class="auth-card">
    <a href="../index.php" class="text-muted-fp small d-inline-flex align-items-center gap-2 mb-4">&larr; Back to shop</a>
    <div class="auth-tabs"><a class="auth-tab active" href="login.php">Login</a><a class="auth-tab" href="register.php">Create Account</a></div>
    <h2 style="font-size:1.7rem;" class="mb-1">Welcome Back</h2><p class="text-muted-fp small mb-4">Login to your account</p>
    <?php if ($flash): ?><div class="alert alert-<?= e($flash['type'] === 'success' ? 'success' : 'danger') ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form id="loginForm" method="post" action="login.php" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="fp-field"><label for="loginEmail">Email Address</label><div class="fp-input-wrap no-icon"><input type="email" name="email" class="fp-input" id="loginEmail" value="<?= e($email) ?>" placeholder="you@example.com" required></div><div class="field-hint"></div></div>
      <div class="fp-field"><label for="loginPassword">Password</label><div class="fp-input-wrap no-icon"><input type="password" name="password" class="fp-input" id="loginPassword" placeholder="Enter your password" required></div><div class="field-hint"></div></div>
      <button type="submit" class="btn-fp-gold">Login</button><p class="text-muted-fp small text-center mt-4 mb-0">New to Falcon Perfumes? <a href="register.php" style="color:var(--gold);">Create an account</a></p>
    </form>
  </div></div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
