<?php
require __DIR__.'/includes/bootstrap.php'; $user=require_login(); $title='My profile'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf(); $action=(string)($_POST['action']??'');
    if($action==='details') {
        $name=trim((string)($_POST['name']??'')); $email=trim((string)($_POST['email']??''));
        if(strlen($name)<2||strlen($name)>100) $error='Name must be between 2 and 100 characters.';
        elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>190) $error='Enter a valid email address.';
        else {
            try { query('UPDATE users SET name=?,email=? WHERE id=?','ssi',[$name,$email,$user['id']]); flash('Profile updated.'); redirect('profile.php'); }
            catch(mysqli_sql_exception $exception) { if($exception->getCode()===1062) $error='That email is already in use.'; else throw $exception; }
        }
    } elseif($action==='password') {
        $password=(string)($_POST['password']??'');
        $stored=query('SELECT password FROM users WHERE id=?','i',[$user['id']])->get_result()->fetch_assoc();
        if(!password_verify((string)($_POST['current_password']??''),$stored['password'])) $error='Current password is incorrect.';
        elseif(strlen($password)<8||strlen($password)>128) $error='New password must be between 8 and 128 characters.';
        elseif($password!==($_POST['password_confirmation']??'')) $error='New passwords do not match.';
        else { query('UPDATE users SET password=? WHERE id=?','si',[password_hash($password,PASSWORD_DEFAULT),$user['id']]); session_regenerate_id(true); flash('Password changed.'); redirect('profile.php'); }
    }
}
require __DIR__.'/includes/header.php';
?>
<div class="page-heading"><span class="eyebrow">YOUR SPACE</span><h1>My profile<span>.</span></h1><p class="muted">Keep your details up to date.</p></div>
<?php if($error): ?><div class="error" role="alert"><?= e($error) ?></div><?php endif; ?>
<div class="two-column"><section class="panel"><h2>The basics</h2><span class="badge"><?= e($user['role']) ?></span><form method="post" class="stack"><?= csrf() ?><input type="hidden" name="action" value="details"><label>Full name<input name="name" value="<?= e($_POST['name']??$user['name']) ?>" required minlength="2" maxlength="100" autocomplete="name"></label><label>Email address<input name="email" type="email" value="<?= e($_POST['email']??$user['email']) ?>" required maxlength="190" autocomplete="email"></label><button class="button">Save details</button></form></section><section class="panel"><h2>Change password</h2><form method="post" class="stack"><?= csrf() ?><input type="hidden" name="action" value="password"><label>Current password<input name="current_password" type="password" autocomplete="current-password" required maxlength="128"></label><label>New password<input name="password" type="password" autocomplete="new-password" minlength="8" maxlength="128" required></label><label>Confirm new password<input name="password_confirmation" type="password" autocomplete="new-password" minlength="8" maxlength="128" required></label><button class="button">Update password</button></form></section></div>
<?php require __DIR__.'/includes/footer.php'; ?>
