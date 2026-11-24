<?php
require __DIR__.'/includes/bootstrap.php'; require_login(); $title='Your bag';
if($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf(); $id=(int)($_POST['id']??0); $action=(string)($_POST['action']??'');
    if($action==='remove') { unset($_SESSION['cart'][$id]); flash('Product removed from your bag.'); }
    elseif(in_array($action,['add','update'],true)) {
        $qty=filter_var($_POST['quantity']??1,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>99]]);
        if(!$qty) flash('Choose a quantity from 1 to 99.');
        elseif(!product($id)) flash('That product is no longer available.');
        else { $_SESSION['cart'][$id]=$action==='add'?min(99,($_SESSION['cart'][$id]??0)+$qty):$qty; flash('Your bag has been updated.'); }
    }
    redirect('cart.php');
}
$items=cart_items(); $total=array_sum(array_column($items,'line_total')); require __DIR__.'/includes/header.php';
?>
<div class="page-heading"><span class="eyebrow">GOOD CHOICES</span><h1>Your shopping bag<span>.</span></h1><p class="muted"><?= cart_count() ?> items, picked by you.</p></div>
<?php if(!$items): ?><div class="empty"><span>＋</span><h2>A little room for something good.</h2><p>Your bag is empty. Explore the essentials to get started.</p><a href="dashboard.php" class="button">Discover the collection ↗</a></div><?php else: ?><div class="cart-layout"><div class="panel cart-items"><?php foreach($items as $p): ?><article class="cart-item"><a href="product_details.php?id=<?= $p['id'] ?>"><img src="<?= e(image_url($p)) ?>" alt="<?= e($p['product_name']) ?>"></a><div><span class="product-label">EVERYDAY ESSENTIAL</span><h3><a href="product_details.php?id=<?= $p['id'] ?>"><?= e($p['product_name']) ?></a></h3><p><?= money($p['price']) ?> each</p><form method="post" class="quantity-form"><?= csrf() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="action" value="update"><label class="sr-only" for="qty-<?= $p['id'] ?>">Quantity for <?= e($p['product_name']) ?></label><input id="qty-<?= $p['id'] ?>" type="number" name="quantity" min="1" max="99" required value="<?= $p['quantity'] ?>"><button class="text-button">Update</button></form><form method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="action" value="remove"><button class="text-button danger">Remove</button></form></div><strong><?= money($p['line_total']/100) ?></strong></article><?php endforeach; ?></div><aside class="panel bag-summary"><span class="eyebrow">THE ROUNDUP</span><h2>Bag summary</h2><div><span>Items (<?= cart_count() ?>)</span><span><?= money($total/100) ?></span></div><hr><div class="summary-total"><strong>Total</strong><strong><?= money($total/100) ?></strong></div><p class="muted small-copy">This practice store supports browsing and a shopping bag. Checkout and payments are not available.</p><a href="dashboard.php#collection" class="button">Keep exploring ↗</a></aside></div><?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>
