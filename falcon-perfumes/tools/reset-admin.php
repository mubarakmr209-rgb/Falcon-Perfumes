<?php
// Run only in a local terminal: php tools/reset-admin.php admin_username
if (PHP_SAPI !== 'cli') {http_response_code(404);exit;}
require __DIR__.'/../includes/db.php';
$username=$argv[1]??'falcon_admin';
if(!preg_match('/^[A-Za-z0-9_]{3,40}$/',$username))exit("Invalid username.\n");
$pass='Falcon!'.bin2hex(random_bytes(8));
query('INSERT INTO admins(username,password,must_change_password) VALUES (?,?,1) ON DUPLICATE KEY UPDATE password=VALUES(password),must_change_password=1',[$username,password_hash($pass,PASSWORD_BCRYPT)]);
echo "Username: $username\nNew temporary password: $pass\nChange it on first login.\n";
