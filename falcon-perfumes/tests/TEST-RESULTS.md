# Verification results

Run: 27 September 2026. PHP 8.3.6, MariaDB 10.11.14, Chromium 131.

77 HTTP/browser assertions passed, followed by the read-only CLI smoke checks. Counts include page-load assertions and named behavior checks; they are not 77 separate test suites. Temporary QA customers, messages, orders and uploaded products were removed from the clean submission database. The XAMPP control panel was not available in the test environment; complete the local XAMPP setup steps in README.md before submission.

## Completed assertions

- PASS: Unauthenticated admin access redirects
- PASS: GET 
- PASS: Page renders 
- PASS: GET new-arrivals.php
- PASS: Page renders new-arrivals.php
- PASS: GET brands.php
- PASS: Page renders brands.php
- PASS: GET contact.php
- PASS: Page renders contact.php
- PASS: GET product.php?id=1
- PASS: Page renders product.php?id=1
- PASS: GET auth/register.php
- PASS: Page renders auth/register.php
- PASS: GET auth/login.php
- PASS: Page renders auth/login.php
- PASS: GET admin/login.php
- PASS: Page renders admin/login.php
- PASS: Unknown product returns 404
- PASS: Missing CSRF rejected
- PASS: Customer registration
- PASS: Bcrypt stored; no plaintext password
- PASS: Duplicate registration rejected
- PASS: SQL injection login rejected
- PASS: Login and session regeneration
- PASS: Customer cannot access admin
- PASS: Contact message stored
- PASS: Add to cart with server price
- PASS: Checkout creates order
- PASS: Checkout decrements stock
- PASS: Tampered total ignored
- PASS: Duplicate checkout does not create a second order
- PASS: Admin login rotates session; forces password change
- PASS: Temporary-password guard protects admin routes
- PASS: Admin password update
- PASS: GET admin/messages.php
- PASS: Stored contact XSS escaped
- PASS: GET product.php?id=1
- PASS: Sold-out badge and disabled button
- PASS: Sold-out add blocked on server
- PASS: Excess quantity blocked
- PASS: Checkout rechecks newly sold-out stock
- PASS: Cancellation restores stock
- PASS: Repeated cancellation cannot restore twice
- PASS: Order lifecycle Pending to Delivered
- PASS: Invalid terminal status transition blocked
- PASS: Disabling customer invalidates active customer access
- PASS: Executable disguised as image rejected
- PASS: Product create and multi-image upload
- PASS: Both uploaded photos stored
- PASS: Referenced brand deletion blocked
- PASS: Product edit persists
- PASS: Product deletion hides storefront
- PASS: Product restore
- PASS: GET new-arrivals.php?category=Unisex
- PASS: Category filter
- PASS: GET new-arrivals.php?q=9+PM
- PASS: Search
- PASS: All images load: 
- PASS: All images load: new-arrivals.php
- PASS: All images load: product.php?id=2
- PASS: All images load: auth/register.php
- PASS: All images load: contact.php
- PASS: Gallery thumbnail changes main image
- PASS: Admin page renders admin/index.php
- PASS: Admin page renders admin/products.php
- PASS: Admin page renders admin/product-edit.php?id=2
- PASS: Admin edit fields render correctly
- PASS: Admin page renders admin/orders.php
- PASS: Admin page renders admin/messages.php
- PASS: Mobile no horizontal overflow: 
- PASS: Mobile no horizontal overflow: new-arrivals.php
- PASS: Mobile no horizontal overflow: product.php?id=2
- PASS: Mobile no horizontal overflow: auth/register.php
- PASS: Mobile no horizontal overflow: contact.php
- PASS: Mobile no horizontal overflow: cart.php
- PASS: Mobile menu opens
- PASS: No browser JavaScript errors

## Reproduce core checks locally

1. Import database.sql into a new local database and run `php tests/smoke.php`.
2. Register a unique customer; reject duplicate email and bad passwords. Sign in and sign out.
3. Sign into admin separately, change the temporary password and ensure a customer session cannot open admin pages.
4. Send a contact message and verify it in the admin inbox.
5. Add an in-stock perfume to the bag, checkout, and verify the order and stock deduction.
6. Mark a product sold out; confirm its badge/disabled button and rejection of a direct cart POST. Mark an item sold out after adding to the bag and verify checkout blocks it.
7. Cancel a pending order; verify stock is restored exactly once. Complete a second order through Confirmed, Shipped and Delivered.
8. Add/edit/archive/restore a product; upload two valid images; reject a PHP payload named as an image. Change the cover photo; keep at least two gallery photos.
9. Disable a customer and verify protected-page access is removed.
10. Use the mobile menu, search, filters and image thumbnails on a phone-size screen.

## Limitations

No multi-server load test, formal penetration test, live payment processing, live hosting or XAMPP GUI automation was performed. The checkout transaction uses database row locks; high-concurrency load behavior was not stress-tested.

## Final package checks

The exact exported SQL was imported into a fresh test database. The packaged admin credential and first-login password change worked. Additional checks passed for passwords containing intentional spaces, changing a gallery cover, removing one image, refusing to remove below two images, admin logout and post-logout access denial. The final exported starter data remains unchanged by those checks.
