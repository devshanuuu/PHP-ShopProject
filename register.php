<?php
require __DIR__.'/includes/bootstrap.php';
if(current_user()) redirect('dashboard.php');
$title='Join Everyday'; $error=''; $name=''; $email=''; $role='user';
if($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf(); $name=trim((string)($_POST['name']??'')); $email=trim((string)($_POST['email']??'')); $password=(string)($_POST['password']??''); $role=(string)($_POST['role']??'user');
    if(strlen($name)<2||strlen($name)>100) $error='Enter a name between 2 and 100 characters.';
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>190) $error='Enter a valid email address.';
    elseif(strlen($password)<8||strlen($password)>128) $error='Use a password between 8 and 128 characters.';
    elseif($password!==($_POST['password_confirmation']??'')) $error='Passwords do not match.';
    elseif(!in_array($role,['user','vendor'],true)) $error='Choose a valid account type.';
    if(!$error) {
        try { query('INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)','ssss',[$name,$email,password_hash($password,PASSWORD_DEFAULT),$role]); flash('Account created. You can log in now.'); redirect('login.php'); }
        catch(mysqli_sql_exception $exception) { if($exception->getCode()===1062) $error='That email is already registered. Try logging in.'; else throw $exception; }
    }
}
require __DIR__.'/includes/header.php';
?>
<div class="auth-layout"><div class="auth-intro"><div class="eyebrow">MAKE YOURSELF AT HOME</div><h1>A little more<br><em>everyday.</em></h1><p>Find the things you will reach for again and again.</p><img src="assets/products/tshirt.avif" alt="Everyday tee"></div><section class="panel auth-panel"><span class="eyebrow">HELLO, YOU</span><h2>Create your account</h2><?php if($error): ?><div class="error" role="alert"><?= e($error) ?></div><?php endif; ?><form method="post" class="stack"><?= csrf() ?><label>Full name<input name="name" autocomplete="name" required minlength="2" maxlength="100" value="<?= e($name) ?>"></label><label>Email address<input name="email" type="email" autocomplete="email" required maxlength="190" value="<?= e($email) ?>"></label><div class="form-grid"><label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" maxlength="128" required></label><label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" maxlength="128" required></label></div><small class="muted">Use at least 8 characters.</small><label>Account type<select name="role"><option value="user">Shopper</option><option value="vendor" <?= $role==='vendor'?'selected':'' ?>>Vendor</option></select></label><button class="button">Create account <span>↗</span></button></form><p class="auth-switch">Already a member? <a href="login.php">Log in</a></p></section></div>
<?php require __DIR__.'/includes/footer.php'; ?>
