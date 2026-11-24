<?php
require __DIR__.'/includes/bootstrap.php';
$p=product((int)($_GET['id']??0));
if(!$p) { http_response_code(404); $title='Product not found'; require __DIR__.'/includes/header.php'; echo '<div class="empty"><h1>Product not found</h1><a class="button" href="dashboard.php">Back to shop</a></div>'; require __DIR__.'/includes/footer.php'; exit; }
$title=$p['product_name']; require __DIR__.'/includes/header.php';
?>
<a href="dashboard.php#collection" class="back-link">← Back to the collection</a><article class="detail-layout"><div class="detail-image"><img src="<?= e(image_url($p)) ?>" alt="<?= e($p['product_name']) ?>"></div><div class="detail-copy"><span class="eyebrow">EVERYDAY ESSENTIAL</span><h1><?= e($p['product_name']) ?></h1><p class="detail-price"><?= money($p['price']) ?></p><p class="description"><?= nl2br(e($p['description'])) ?></p><hr><?php if($viewer): ?><form action="cart.php" method="post" class="stack"><?= csrf() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="action" value="add"><label>Quantity<input class="quantity" type="number" name="quantity" min="1" max="99" value="1" required></label><button class="button">Add to bag <span>+</span></button></form><?php else: ?><a href="login.php" class="button">Log in to add to bag ↗</a><?php endif; ?><p class="muted small-copy">A considered addition to your everyday.</p></div></article>
<?php require __DIR__.'/includes/footer.php'; ?>
