<?php
require __DIR__.'/includes/bootstrap.php';
$title='Everyday essentials';
$search=trim((string)($_GET['q']??''));
$sort=(string)($_GET['sort']??'newest');
$sort_options=['newest'=>'id DESC','price_asc'=>'price ASC, id DESC','price_desc'=>'price DESC, id DESC'];
if (!isset($sort_options[$sort])) $sort='newest';
// The search value is bound; ORDER BY uses only the allowed strings above.
$where=$search!==''?' WHERE product_name LIKE ?':'';
$types=$search!==''?'s':''; $params=$search!==''?['%'.$search.'%']:[];
$total=(int)query('SELECT COUNT(*) AS total FROM products'.$where,$types,$params)->get_result()->fetch_assoc()['total'];
$per_page=6; $pages=max(1,(int)ceil($total/$per_page));
$page=max(1,min($pages,(int)($_GET['page']??1))); $offset=($page-1)*$per_page;
$products=query('SELECT * FROM products'.$where.' ORDER BY '.$sort_options[$sort].' LIMIT ? OFFSET ?',$types.'ii',[...$params,$per_page,$offset])->get_result()->fetch_all(MYSQLI_ASSOC);
function page_url(int $number): string { global $search,$sort; return 'dashboard.php?'.http_build_query(['q'=>$search,'sort'=>$sort,'page'=>$number]).'#collection'; }
require __DIR__.'/includes/header.php';
?>
<section class="hero"><div class="hero-copy"><div class="eyebrow"><span></span> THE EVERYDAY EDIT</div><h1>Good things.<br>For your <em>everyday.</em></h1><p>Easy layers, favourite finds, and the finishing touches.<br>Discover essentials that feel like you.</p><a href="#collection" class="button">Explore the collection <span>↗</span></a><div class="hero-note">Thoughtfully chosen. Simply styled.</div></div><div class="hero-art"><div class="art-label">LESS, BUT BETTER.<br><strong>The essentials edit / 01</strong></div><img src="assets/products/jacket.avif" alt="Jacket from the Everyday collection"><div class="art-sticker">Made for<br><em>repeat wear.</em></div><span class="art-caption">YOUR NEXT FAVOURITE IS HERE</span></div></section>
<section class="collection" id="collection"><div class="section-heading"><div><div class="eyebrow">THE COLLECTION</div><h2>Find your next favourite<span>.</span></h2></div><span class="muted"><?= $total ?> <?= $total===1?'essential':'essentials' ?> to discover</span></div>
<form class="filters" method="get" action="dashboard.php#collection"><div class="search-field"><span aria-hidden="true">⌕</span><input aria-label="Search products" name="q" value="<?= e($search) ?>" placeholder="Search for something good…" maxlength="150"></div><label class="sort-label">Sort by <select name="sort"><option value="newest" <?= $sort==='newest'?'selected':'' ?>>Newest arrivals</option><option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price: low to high</option><option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: high to low</option></select></label><button class="button small">Apply</button><?php if($search!==''||$sort!=='newest'): ?><a href="dashboard.php#collection" class="subtle-link">Clear</a><?php endif; ?></form>
<?php if(!$products): ?><div class="empty"><span>⌕</span><h3>No matches this time</h3><p>Try another product name or explore the full collection.</p><a class="button" href="dashboard.php">View all products</a></div><?php else: ?>
<div class="product-grid"><?php foreach($products as $p): ?><article class="product-card"><a href="product_details.php?id=<?= $p['id'] ?>" class="product-image"><img loading="lazy" src="<?= e(image_url($p)) ?>" alt="<?= e($p['product_name']) ?>"><span class="image-arrow" aria-hidden="true">↗</span></a><div class="product-info"><div><span class="product-label">EVERYDAY ESSENTIAL</span><h3><a href="product_details.php?id=<?= $p['id'] ?>"><?= e($p['product_name']) ?></a></h3><p><?= money($p['price']) ?></p></div><?php if($viewer): ?><form action="cart.php" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="action" value="add"><button class="add-button" aria-label="Add <?= e($p['product_name']) ?> to bag">+</button></form><?php else: ?><a class="add-button" href="login.php" aria-label="Log in to add <?= e($p['product_name']) ?> to bag">+</a><?php endif; ?></div></article><?php endforeach; ?></div>
<nav class="pagination" aria-label="Product pages"><span class="muted">Showing <?= $offset+1 ?>–<?= min($offset+$per_page,$total) ?> of <?= $total ?></span><div><?php if($page>1): ?><a href="<?= e(page_url($page-1)) ?>" aria-label="Previous page">←</a><?php endif; ?><?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?><a <?= $i===$page?'aria-current="page"':'' ?> href="<?= e(page_url($i)) ?>"><?= $i ?></a><?php endfor; ?><?php if($page<$pages): ?><a href="<?= e(page_url($page+1)) ?>" aria-label="Next page">→</a><?php endif; ?></div></nav><?php endif; ?></section>
<section class="bottom-note"><span class="eyebrow">THE EVERYDAY PHILOSOPHY</span><h2>Keep it simple.<br>Make it yours.</h2><p>Great style starts with the things<br>you reach for every day.</p></section>
<?php require __DIR__.'/includes/footer.php'; ?>
