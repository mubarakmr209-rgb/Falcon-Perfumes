<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_login();

$perfumes = [];
try {
    $perfumes = db()->query('SELECT name, brand, category, description, price FROM perfumes ORDER BY id DESC LIMIT 4')->fetchAll();
} catch (PDOException $exception) {
    error_log('Dashboard catalogue query failed: ' . $exception->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Dashboard - Falcon Perfumes</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="topbar">WELCOME TO YOUR FALCON PERFUMES ACCOUNT</div>
<nav class="navbar navbar-expand-lg fp-navbar"><div class="container"><a class="navbar-brand fp-brand" href="index.php"><span>FALCON<small>PERFUMES</small></span></a><div class="ms-auto d-flex gap-3"><a class="fp-icon-btn" href="index.php">Shop</a><a class="fp-icon-btn" href="contact.php">Contact</a><a class="fp-icon-btn" href="auth/logout.php">Logout</a></div></div></nav>
<main class="section"><div class="container"><div class="section-head"><span class="eyebrow">Your Account</span><h1>Welcome back, <?= e(current_user_name()) ?>.</h1><p class="text-muted-fp">Explore the latest selections added to the Falcon Perfumes catalogue.</p></div>
<div class="row g-4">
<?php foreach ($perfumes as $perfume): ?>
  <div class="col-md-6 col-lg-3"><article class="product-card h-100"><div class="product-body"><span class="eyebrow"><?= e($perfume['category']) ?></span><h2 class="product-name mt-2"><?= e($perfume['name']) ?></h2><p class="text-muted-fp small mb-2"><?= e($perfume['brand']) ?></p><p class="text-muted-fp small"><?= e($perfume['description']) ?></p><strong style="color:var(--gold);">$<?= e(number_format((float) $perfume['price'], 2)) ?></strong></div></article></div>
<?php endforeach; ?>
<?php if (!$perfumes): ?><div class="col-12"><div class="alert alert-warning">The perfume catalogue is unavailable. Confirm that database.sql was imported.</div></div><?php endif; ?>
</div></div></main>
<footer class="fp-footer" id="site-footer"></footer><script src="js/footer.js"></script>
</body></html>
