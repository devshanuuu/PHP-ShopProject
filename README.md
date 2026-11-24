# Everyday — PHP Shopping App

A small shopping app built with **plain PHP, MySQL, HTML, CSS, and vanilla JavaScript**. It started as my local login, product CRUD, and shopping-cart practice project. This version refreshes the interface and adds four beginner-friendly features without using a framework.

## What's new

- **Product search:** find products by name using a prepared `LIKE` query.
- **Price sorting:** newest first, low to high, or high to low. The SQL sort order comes from an allowlist.
- **Pagination:** six products per page; search and sort choices stay selected while changing pages.
- **My profile:** update your name and email, or change your password after entering your current password.

The UI includes responsive product cards, product details, a shopping bag, login/register forms, and matching management pages. Existing functionality includes shopper/vendor registration, password hashing, product CRUD, admin user management, and a session-based shopping cart with quantity updates.

## Run locally with XAMPP

**Requirements:** PHP 8.2+ with `mysqli`, `mysqlnd`, and `fileinfo`; MySQL 8+ or MariaDB 10.4+. XAMPP includes these extensions in typical installations. No Composer, Node, or build step is needed.

1. Clone/download this repository into your XAMPP `htdocs` folder as `everyday-php-shop`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open phpMyAdmin and import `database/schema.sql`. This creates a new database named `everyday_shop` with `users` and `products` tables.
4. Copy `config.example.php` to `config.local.php`. Set your local database username/password. The defaults are `root` with an empty password, for local XAMPP only. The local config is excluded from Git.
5. In a terminal in the project folder, run:

   ```sh
   php scripts/seed.php
   ```

   On Windows, if PHP is not on PATH, use:

   ```powershell
   C:\xampp\php\php.exe scripts\seed.php
   ```

6. Open `http://localhost/everyday-php-shop/`.

Alternatively, after starting MySQL and importing/seeding the database, run `php -S 127.0.0.1:8000` in the project folder and open `http://127.0.0.1:8000`.

**Use a fresh database for this version.** The original folder did not contain an SQL export. The included schema is reconstructed from the PHP code, with product ownership added for vendor permissions. It is not an in-place migration for the old `shanu` database.

## Local demo accounts

The seed script creates these accounts if their emails do not already exist:

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@example.com | DemoPass123! |
| Shopper | shopper@example.com | DemoPass123! |
| Vendor | vendor@example.com | DemoPass123! |

Run the seed script only for local practice. It skips existing accounts without resetting their passwords and skips products with existing sample names. Sample product names and prices are fictional, and some sample entries reuse the original supplied product images.

## Roles

| Action | Visitor | Shopper | Vendor | Admin |
| --- | --- | --- | --- | --- |
| Browse, search, sort, view details | Yes | Yes | Yes | Yes |
| Shopping bag and own profile | No | Yes | Yes | Yes |
| Add products | No | No | Yes | Yes |
| Edit/delete own products | No | No | Yes | Yes |
| Manage all products and users | No | No | No | Yes |

Public registration supports shopper and vendor accounts, as in the original project. Admin accounts are created by the local seed script or an existing admin. Admin accounts cannot be deleted or demoted through the management UI.

## Project structure

```text
assets/                 CSS, JavaScript, and supplied sample images
includes/bootstrap.php  Session, escaping, prepared queries, auth, CSRF, cart helpers
includes/header.php     Shared navigation and flash messages
includes/footer.php     Shared footer
includes/product_form.php Shared add/edit product form
*.php                   Individual pages and POST action handlers
config.example.php      Local database configuration template
database/schema.sql     Fresh database schema
scripts/seed.php        CLI-only demo accounts and sample products
uploads/                User-uploaded product images (ignored by Git)
docs/                   Screenshots and verification notes
```

## Basic security improvements

- Prepared statements for user-provided SQL values and an allowlist for sorting.
- Escaped output with `htmlspecialchars`.
- CSRF tokens and POST requests for changes, including delete and logout.
- `password_hash` / `password_verify`, session ID regeneration, and HTTP-only session cookies.
- Server-side role checks and vendor ownership checks on product operations.
- Random upload names, file size/type checks, and restricted executable uploads.
- No private user records, real passwords, or local configuration in the repository.

## Scope and limitations

This is a fresher portfolio/practice project. The bag is stored in the current session and cleared on logout. It does not include checkout, payments, order history, inventory, password recovery, or email verification. Login rate limiting and production deployment hardening are outside this project's scope. Google Fonts is optional; system fonts are used if it cannot load. Sample product images were supplied with the original practice project.

GitHub hosts the source code; **GitHub Pages cannot run PHP or MySQL**. Use the local setup above to run the app.

See [verification notes](docs/VERIFICATION.md) for checks performed.
