<?php
require __DIR__.'/includes/bootstrap.php'; require_role(['admin']);
if($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); header('Allow: POST'); exit('Use the Delete button.'); }
check_csrf();
// Protect all admin accounts, including the current admin.
$result=query("DELETE FROM users WHERE id=? AND role<>'admin'",'i',[(int)($_POST['id']??0)]);
flash($result->affected_rows?'User deleted.':'Account not found or protected.'); redirect('manage_users.php');
