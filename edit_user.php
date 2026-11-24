<?php
require __DIR__.'/includes/bootstrap.php'; $user=require_role(['admin']); $title='Edit user'; $error='';
$row=query('SELECT id,name,email,role FROM users WHERE id=?','i',[(int)($_GET['id']??0)])->get_result()->fetch_assoc();
if(!$row) { http_response_code(404); exit('User not found.'); }
if($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf(); $name=trim((string)($_POST['name']??'')); $email=trim((string)($_POST['email']??'')); $role=(string)($_POST['role']??'');
    if(strlen($name)<2||strlen($name)>100) $error='Use a name between 2 and 100 characters.';
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>190) $error='Enter a valid email address.';
    elseif(!in_array($role,['user','vendor','admin'],true)) $error='Choose a valid role.';
    elseif($row['role']==='admin' && $role!=='admin') $error='Admin accounts cannot be demoted here.';
    if(!$error) {
        try { query('UPDATE users SET name=?,email=?,role=? WHERE id=?','sssi',[$name,$email,$role,$row['id']]); flash('User updated.'); redirect('manage_users.php'); }
        catch(mysqli_sql_exception $exception) { if($exception->getCode()===1062) $error='That email is already in use.'; else throw $exception; }
    }
}
require __DIR__.'/includes/header.php';
?>
<a href="manage_users.php" class="back-link">← Back to users</a><div class="page-heading"><span class="eyebrow">ACCOUNT DETAILS</span><h1>Edit user<span>.</span></h1></div><section class="panel narrow"><?php if($error): ?><div class="error" role="alert"><?= e($error) ?></div><?php endif; ?><form method="post" class="stack"><?= csrf() ?><label>Full name<input name="name" value="<?= e($_POST['name']??$row['name']) ?>" required minlength="2" maxlength="100"></label><label>Email address<input type="email" name="email" value="<?= e($_POST['email']??$row['email']) ?>" required maxlength="190"></label><label>Role<select name="role"><?php foreach(['user','vendor','admin'] as $role): ?><option value="<?= $role ?>" <?= ($_POST['role']??$row['role'])===$role?'selected':'' ?>><?= ucfirst($role) ?></option><?php endforeach; ?></select></label><button class="button">Save changes</button></form></section>
<?php require __DIR__.'/includes/footer.php'; ?>
