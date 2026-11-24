<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params(['httponly'=>true, 'samesite'=>'Lax', 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}
require_once __DIR__ . '/../db.php';
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function money($value): string { return '₹' . number_format((float)$value, 2); }
function redirect(string $page): void { header('Location: ' . $page); exit; }
function query(string $sql, string $types = '', array $params = []): mysqli_stmt {
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($types !== '') $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt;
}
function csrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
function check_csrf(): void {
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(403); exit('Your form expired. Reload the page and try again.');
    }
}
function flash(string $message): void { $_SESSION['flash'] = $message; }
function current_user(): ?array {
    static $loaded = false, $user = null;
    if (!$loaded) {
        $loaded = true;
        if (!empty($_SESSION['user_id'])) $user = query('SELECT id,name,email,role FROM users WHERE id=?', 'i', [(int)$_SESSION['user_id']])->get_result()->fetch_assoc();
    }
    return $user;
}
function require_login(): array {
    $user = current_user();
    if (!$user) { unset($_SESSION['user_id'], $_SESSION['cart']); redirect('login.php'); }
    return $user;
}
function require_role(array $roles): array {
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) { http_response_code(403); exit('Access denied.'); }
    return $user;
}
function product(int $id): ?array { return query('SELECT * FROM products WHERE id=?','i',[$id])->get_result()->fetch_assoc(); }
function image_url(array $product): string {
    $name = basename($product['product_img'] ?? '');
    if ($name && is_file(__DIR__ . '/../uploads/' . $name)) return 'uploads/' . rawurlencode($name);
    if ($name && is_file(__DIR__ . '/../assets/products/' . $name)) return 'assets/products/' . rawurlencode($name);
    return 'assets/placeholder.svg';
}
function cart_count(): int { return array_sum($_SESSION['cart'] ?? []); }
function cart_items(): array {
    $items=[];
    foreach ($_SESSION['cart'] ?? [] as $id=>$qty) {
        $p = product((int)$id);
        if (!$p) { unset($_SESSION['cart'][$id]); continue; }
        $p['quantity'] = max(1,min(99,(int)$qty));
        $p['line_total'] = (int)round((float)$p['price']*100) * $p['quantity'];
        $items[] = $p;
    }
    return $items;
}
function save_image(?string $existing = null): ?string {
    $file = $_FILES['product_img'] ?? null;
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return $existing;
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2*1024*1024) throw new RuntimeException('Choose an image smaller than 2 MB.');
    $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/avif'=>'avif'];
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime]) || !getimagesize($file['tmp_name'])) throw new RuntimeException('Use a JPG, PNG, WebP, or AVIF image.');
    $filename=bin2hex(random_bytes(16)).'.'.$allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__.'/../uploads/'.$filename)) throw new RuntimeException('Image could not be saved. Check uploads folder permissions.');
    return $filename;
}
