<?php
require __DIR__.'/includes/bootstrap.php'; $user=require_role(['admin','vendor']);
if($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); header('Allow: POST'); exit('Use the Delete button.'); }
check_csrf(); $p=product((int)($_POST['id']??0));
if(!$p) { http_response_code(404); exit('Product not found.'); }
if($user['role']!=='admin' && (int)$p['created_by']!==(int)$user['id']) { http_response_code(403); exit('Access denied.'); }
query('DELETE FROM products WHERE id=?','i',[$p['id']]); unset($_SESSION['cart'][$p['id']]); flash('Product deleted.'); redirect('manage_products.php');
