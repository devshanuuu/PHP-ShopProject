<?php
require __DIR__.'/includes/bootstrap.php';
if(current_user()) redirect('dashboard.php');
$title='Welcome back'; $error=''; $email='';
if($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf(); $email=trim((string)($_POST['email']??'')); $password=(string)($_POST['password']??'');
    $account=query('SELECT * FROM users WHERE email=?','s',[$email])->get_result()->fetch_assoc();
    if($account && password_verify($password,$account['password'])) {
        session_regenerate_id(true); $_SESSION['user_id']=$account['id']; unset($_SESSION['cart']); flash('Welcome back, '.$account['name'].'.'); redirect('dashboard.php');
    }
    $error='Email or password is incorrect.';
}
require __DIR__.'/includes/header.php';
?>
<div class="auth-layout"><div class="auth-intro"><div class="eyebrow">YOUR EVERYDAY, UPGRADED</div><h1>Good to<br>see you <em>again.</em></h1><p>Your favourite essentials are waiting.</p><img src="assets/products/shoes.avif" alt="Everyday sneakers"></div><section class="panel auth-panel"><span class="eyebrow">WELCOME BACK</span><h2>Log in to Everyday</h2><p class="muted">A few good finds are just around the corner.</p><?php if($error): ?><div class="error" role="alert"><?= e($error) ?></div><?php endif; ?><form method="post" class="stack"><?= csrf() ?><label>Email address<input name="email" type="email" autocomplete="email" required maxlength="190" value="<?= e($email) ?>" placeholder="you@example.com"></label><label>Password<input name="password" type="password" autocomplete="current-password" required maxlength="128" placeholder="Your password"></label><button class="button">Log in <span>↗</span></button></form><p class="auth-switch">New around here? <a href="register.php">Create an account</a></p></section></div>
<?php require __DIR__.'/includes/footer.php'; ?>
