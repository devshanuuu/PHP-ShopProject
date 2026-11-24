<?php
require __DIR__.'/bootstrap.php'; $user=require_role(['admin','vendor']);
$editing=basename($_SERVER['SCRIPT_NAME'])==='edit_product.php';
$p=$editing?product((int)($_GET['id']??0)):['product_name'=>'','price'=>'','description'=>'','product_img'=>null];
if(!$p) { http_response_code(404); exit('Product not found.'); }
if($editing && $user['role']!=='admin' && (int)$p['created_by']!==(int)$user['id']) { http_response_code(403); exit('Access denied.'); }
$title=$editing?'Edit product':'Add product'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf(); $name=trim((string)($_POST['product_name']??'')); $price=(string)($_POST['price']??''); $description=trim((string)($_POST['description']??''));
    if(strlen($name)<2||strlen($name)>150) $error='Use a product name between 2 and 150 characters.';
    elseif(!preg_match('/^\d{1,7}(\.\d{1,2})?$/',$price)||(float)$price<=0) $error='Enter a price greater than zero, with at most two decimal places (maximum 9,999,999.99).';
    elseif(strlen($description)>5000) $error='Keep the description under 5,000 characters.';
    if(!$error) {
        try {
            $image=save_image($p['product_img']);
            if($editing) query('UPDATE products SET product_name=?,price=?,description=?,product_img=? WHERE id=?','sdssi',[$name,(float)$price,$description,$image,$p['id']]);
            else query('INSERT INTO products(product_name,price,description,product_img,created_by) VALUES(?,?,?,?,?)','sdssi',[$name,(float)$price,$description,$image,$user['id']]);
            flash($editing?'Product updated.':'Product added.'); redirect('manage_products.php');
        } catch(RuntimeException $exception) { $error=$exception->getMessage(); }
    }
}
require __DIR__.'/header.php';
?>
<a class="back-link" href="manage_products.php">← Back to products</a><div class="page-heading"><span class="eyebrow">YOUR CATALOGUE</span><h1><?= e($title) ?><span>.</span></h1></div><section class="panel narrow"><?php if($error): ?><div class="error" role="alert"><?= e($error) ?></div><?php endif; ?><form method="post" enctype="multipart/form-data" class="stack"><?= csrf() ?><label>Product name<input name="product_name" value="<?= e($_POST['product_name']??$p['product_name']) ?>" minlength="2" maxlength="150" required></label><label>Price (INR)<input type="number" name="price" value="<?= e($_POST['price']??$p['price']) ?>" min="0.01" max="9999999.99" step="0.01" required></label><label>Description<textarea name="description" rows="5" maxlength="5000"><?= e($_POST['description']??$p['description']) ?></textarea></label><?php if($p['product_img']): ?><img class="preview-image" src="<?= e(image_url($p)) ?>" alt="Current product image"><?php endif; ?><label>Product image<input type="file" name="product_img" accept="image/jpeg,image/png,image/webp,image/avif"></label><small class="muted">Optional · JPG, PNG, WebP or AVIF · up to 2 MB</small><div class="form-actions"><button class="button"><?= $editing?'Save changes':'Add product' ?></button><a href="manage_products.php" class="subtle-link">Cancel</a></div></form></section>
<?php require __DIR__.'/footer.php'; ?>
