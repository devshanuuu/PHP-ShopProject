<?php
// Run in your terminal only: php scripts/seed.php
if(PHP_SAPI!=='cli') { http_response_code(403); exit('CLI only.'); }
require __DIR__.'/../db.php';
$accounts=[['Demo Admin','admin@example.com','admin'],['Demo Shopper','shopper@example.com','user'],['Demo Vendor','vendor@example.com','vendor']];
foreach($accounts as [$name,$email,$role]) {
    $check=$conn->prepare('SELECT id FROM users WHERE email=?'); $check->bind_param('s',$email); $check->execute();
    if($check->get_result()->num_rows) { echo "Skipped existing account: $email\n"; continue; }
    $password=password_hash('DemoPass123!',PASSWORD_DEFAULT);
    $stmt=$conn->prepare('INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)'); $stmt->bind_param('ssss',$name,$email,$password,$role); $stmt->execute();
}
$owner=(int)$conn->query("SELECT id FROM users WHERE email='admin@example.com'")->fetch_assoc()['id'];
$products=[
 ['The Everyday Tee',699,'A soft, easy-going tee for your everyday rotation. Simple to style, easy to wear.','tshirt.avif'],
 ['Classic Denim',1899,'Your everyday denim essential. Pair with a favourite tee and make it your own.','Jean.avif'],
 ['Weekend Sneakers',2499,'A clean, casual pair to complete your off-duty look. Made for everyday styling.','shoes.avif'],
 ['The Minimal Watch',1599,'A simple finishing touch for your daily wardrobe. An understated everyday accessory.','watch.avif'],
 ['The Layering Jacket',3299,'An easy outer layer to take your everyday outfit a little further.','jacket.avif'],
 ['Off-Duty Tee',799,'A relaxed wardrobe staple for slower mornings and casual evenings.','tshirt.avif'],
 ['City Denim',2199,'A versatile denim essential that works across your weekly rotation.','Jean.avif'],
 ['Daily Sneakers',2799,'Everyday footwear with a clean, simple look. Easy to pair with denim or casual layers.','shoes.avif'],
 ['Weekend Layer',2899,'A wardrobe layer for effortless weekend styling. Keep your outfit simple and considered.','jacket.avif'],
];
foreach($products as [$name,$price,$description,$image]) {
    $check=$conn->prepare('SELECT id FROM products WHERE product_name=?'); $check->bind_param('s',$name); $check->execute();
    if($check->get_result()->num_rows) continue;
    $stmt=$conn->prepare('INSERT INTO products(product_name,price,description,product_img,created_by) VALUES(?,?,?,?,?)'); $stmt->bind_param('sdssi',$name,$price,$description,$image,$owner); $stmt->execute();
}
echo "Demo setup complete. Emails: admin@example.com / shopper@example.com / vendor@example.com\nPassword for NEW demo accounts: DemoPass123!\nOnly use these accounts for local practice. Existing passwords were not changed.\n";
