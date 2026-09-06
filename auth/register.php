<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
start_app_session();

if (is_logged_in()) {
    redirect('../dashboard.php');
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (!verify_csrf()) {
        $errors['general'] = 'Your form session expired. Please try again.';
    } else {
        $errors = validate_registration($username, $email, $password);
    }

    if (!$errors) {
        try {
            $check = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $check->execute(['email' => $email]);

            if ($check->fetch()) {
                $errors['email'] = 'An account already exists for this email address.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $insert = db()->prepare(
                    'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
                );
                $insert->execute([
                    'username' => $username,
                    'email' => $email,
                    'password' => $passwordHash,
                ]);

                set_flash('success', 'Account created. You can now log in.');
                redirect('login.php');
            }
        } catch (PDOException $exception) {
            error_log('Registration failed: ' . $exception->getMessage());
            $errors['general'] = 'We could not create your account. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account - Falcon Perfumes</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-shell">
  <div class="auth-side"><div class="text-center position-relative"><div class="fp-brand justify-content-center" style="font-size:2rem;"><span>FALCON<small>PERFUMES</small></span></div><p class="text-muted-fp mt-3" style="max-width:340px;">Create an account to make every scent discovery yours.</p></div></div>
  <div class="auth-form-side"><div class="auth-card">
    <a href="../index.php" class="text-muted-fp small d-inline-flex align-items-center gap-2 mb-4">&larr; Back to shop</a>
    <div class="auth-tabs"><a class="auth-tab" href="login.php">Login</a><a class="auth-tab active" href="register.php">Create Account</a></div>
    <h2 style="font-size:1.7rem;" class="mb-1">Create Account</h2><p class="text-muted-fp small mb-4">Join Falcon Perfumes today</p>
    <?php if (!empty($errors['general'])): ?><div class="alert alert-danger"><?= e($errors['general']) ?></div><?php endif; ?>
    <form id="signupForm" method="post" action="register.php" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="fp-field"><label for="signupName">Username</label><div class="fp-input-wrap no-icon"><input type="text" name="username" class="fp-input<?= isset($errors['username']) ? ' is-invalid' : '' ?>" id="signupName" value="<?= e($username) ?>" placeholder="Your username" minlength="3" maxlength="80" required></div><div class="field-hint err"><?= e($errors['username'] ?? '') ?></div></div>
      <div class="fp-field"><label for="signupEmail">Email Address</label><div class="fp-input-wrap no-icon"><input type="email" name="email" class="fp-input<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="signupEmail" value="<?= e($email) ?>" placeholder="you@example.com" maxlength="120" required></div><div class="field-hint err"><?= e($errors['email'] ?? '') ?></div></div>
      <div class="fp-field"><label for="signupPassword">Password</label><div class="fp-input-wrap no-icon"><input type="password" name="password" class="fp-input<?= isset($errors['password']) ? ' is-invalid' : '' ?>" id="signupPassword" placeholder="At least 8 characters" minlength="8" required></div><div class="field-hint err"><?= e($errors['password'] ?? '') ?></div></div>
      <button type="submit" class="btn-fp-gold">Create Account</button><p class="text-muted-fp small text-center mt-4 mb-0">Already have an account? <a href="login.php" style="color:var(--gold);">Login</a></p>
    </form>
  </div></div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
