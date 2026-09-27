<?php require '_init.php';if(!post()){http_response_code(405);header('Allow: POST');exit;}verify_csrf();end_session();redirect('admin/login.php');
