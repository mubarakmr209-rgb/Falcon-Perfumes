<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
start_app_session();

$errors = [];
$name = is_logged_in() ? current_user_name() : '';
$email = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    if (!verify_csrf()) {
        $errors['general'] = 'Your form session expired. Please try again.';
    }
    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors['name'] = 'Please enter a name between 2 and 100 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (mb_strlen($subject) > 120) {
        $errors['subject'] = 'The subject must be 120 characters or fewer.';
    }
    if (mb_strlen($message) < 10 || mb_strlen($message) > 2000) {
        $errors['message'] = 'Your message must be between 10 and 2,000 characters.';
    }

    if (!$errors) {
        $storedMessage = $subject !== '' ? "Subject: {$subject}\n\n{$message}" : $message;

        try {
            $statement = db()->prepare(
                'INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)'
            );
            $statement->execute([
                'name' => $name,
                'email' => $email,
                'message' => $storedMessage,
            ]);

            set_flash('success', 'Thank you - your message was sent successfully. We will reply soon.');
            redirect('contact.php');
        } catch (PDOException $exception) {
            error_log('Contact message insert failed: ' . $exception->getMessage());
            $errors['general'] = 'We could not send your message. Please try again.';
        }
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us - Falcon Perfumes</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="topbar">FREE DELIVERY ON ORDERS OVER $50 &nbsp;•&nbsp; 100% AUTHENTIC PERFUMES</div>
<nav class="navbar navbar-expand-lg fp-navbar"><div class="container"><a class="navbar-brand fp-brand" href="index.php"><span>FALCON<small>PERFUMES</small></span></a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#fpNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="fpNav"><ul class="navbar-nav mx-auto"><li class="nav-item"><a class="nav-link" href="index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="new-arrivals.php">New Arrivals</a></li><li class="nav-item"><a class="nav-link" href="brands.php">Brands</a></li><li class="nav-item"><a class="nav-link active" href="contact.php">Contact Us</a></li></ul><div class="d-flex align-items-center gap-3 mt-3 mt-lg-0"><?php if (is_logged_in()): ?><a href="dashboard.php" class="fp-icon-btn">Dashboard</a><a href="auth/logout.php" class="fp-icon-btn">Logout</a><?php else: ?><a href="auth/login.php" class="fp-icon-btn">Login</a><?php endif; ?></div></div></div></nav>

<section class="section"><div class="container"><div class="section-head"><span class="eyebrow">We Are Here To Help</span><h1>Contact Falcon Perfumes</h1><p class="text-muted-fp">Send us a message and our fragrance team will get back to you.</p></div>
<div class="row g-4">
  <div class="col-lg-7"><div class="contact-panel h-100"><h2 class="mb-2" style="font-size:1.5rem;">Get in Touch</h2><p class="text-muted-fp small mb-4">Your message is securely saved in our customer-support system.</p>
  <?php if ($flash): ?><div class="alert alert-success" role="alert"><?= e($flash['message']) ?></div><?php endif; ?>
  <?php if (!empty($errors['general'])): ?><div class="alert alert-danger" role="alert"><?= e($errors['general']) ?></div><?php endif; ?>
  <form id="contactForm" method="post" action="contact.php" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="fp-field"><label for="contactName">Your Name</label><div class="fp-input-wrap no-icon"><input type="text" name="name" class="fp-input<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="contactName" value="<?= e($name) ?>" maxlength="100" placeholder="Jane Doe" required></div><div class="field-hint err"><?= e($errors['name'] ?? '') ?></div></div>
    <div class="fp-field"><label for="contactEmail">Your Email</label><div class="fp-input-wrap no-icon"><input type="email" name="email" class="fp-input<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="contactEmail" value="<?= e($email) ?>" maxlength="120" placeholder="you@example.com" required></div><div class="field-hint err"><?= e($errors['email'] ?? '') ?></div></div>
    <div class="fp-field"><label for="contactSubject">Subject <span class="text-muted-fp">(optional)</span></label><div class="fp-input-wrap no-icon"><input type="text" name="subject" class="fp-input<?= isset($errors['subject']) ? ' is-invalid' : '' ?>" id="contactSubject" value="<?= e($subject) ?>" maxlength="120" placeholder="How can we help?"></div><div class="field-hint err"><?= e($errors['subject'] ?? '') ?></div></div>
    <div class="fp-field"><label for="contactMessage">Your Message</label><textarea name="message" class="fp-input<?= isset($errors['message']) ? ' is-invalid' : '' ?>" id="contactMessage" rows="5" maxlength="2000" placeholder="Write your message here..." required><?= e($message) ?></textarea><div class="field-hint err"><?= e($errors['message'] ?? '') ?></div></div>
    <button type="submit" class="btn-fp-gold">Send Message</button>
  </form></div></div>
  <div class="col-lg-5"><div class="contact-panel h-100"><h2 class="mb-4" style="font-size:1.5rem;">Our Details</h2><div class="contact-detail"><div><strong>Phone</strong><span>+94 77 123 4567</span></div></div><div class="contact-detail"><div><strong>Email</strong><span>info@falconperfumes.com</span></div></div><div class="contact-detail"><div><strong>Hours</strong><span>Sat-Thu: 9:00 AM - 9:00 PM<br>Sunday: 10:00 AM - 6:00 PM</span></div></div><div class="contact-detail"><div><strong>Address</strong><span>No. 123, Main Street,<br>Colombo, Sri Lanka</span></div></div></div></div>
</div></div></section>
<footer class="fp-footer" id="site-footer"></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="js/script.js"></script><script src="js/footer.js"></script>
</body></html>
