<?php
require __DIR__.'/includes/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); header('Allow: POST'); exit('Use the Log out button.'); }
check_csrf(); $_SESSION=[]; session_regenerate_id(true); flash('You have logged out.'); redirect('login.php');
