# Verification

Executed against PHP 8.4.27 and a fresh MariaDB 11.8.6 database on 25 November 2026 (India time).

- All 22 distributable PHP files passed `php -l` syntax checks.
- 42 HTTP integration checks passed, covering:
  - Public storefront and six-product pagination, including the second page.
  - Name search, both price sort directions, empty results, and invalid query controls.
  - Search SQL injection payload treated as literal text.
  - Missing-product 404 and anonymous cart redirection.
  - CSRF rejection and POST-only destructive actions/logout.
  - Add, update, remove, invalid quantity handling, and exact shopping-bag totals.
  - Shopper access restrictions and vendor ownership restrictions.
  - Profile updates, duplicate-email rejection, current-password verification, password change, and login with the new password.
  - Vendor product creation, edit, deletion, and escaped stored content.
  - Admin user management, protected admin deletion/demotion, public admin-registration rejection, and shopper registration/login.
  - CSS and all five bundled product images return HTTP 200.

The local test database contained only synthetic demo accounts. Test accounts/products were removed, and the demo shopper details/password restored after checks.

## Visual verification

Responsive CSS is included for desktop, tablet, and phone widths. A rendered browser review could not be completed in this environment because the browser could not open the local preview. Visual layout still needs a browser check after local setup. No unverified screenshots are included.

## Manual review after setup

1. Open the collection at desktop and phone widths; check card spacing, navigation, form labels, and horizontal overflow.
2. Search for `Tee`, sort by price, clear search, then visit page 2.
3. Log in as the shopper; add a product, change quantity, and remove it.
4. Update profile details and change the password using the current password.
5. Log in as vendor; add a product with an image, edit it, and delete it.
6. Log in as admin; check all-product and user management.
