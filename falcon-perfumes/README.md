# Falcon Perfumes — Phase 3 PHP & MySQL

A complete local perfume shop based on the four supplied Falcon Perfumes HTML pages. Includes PHP customer authentication, MySQL data persistence, contact messages, product galleries, server-side cart/checkout and a separate protected admin panel. No Composer, npm, CDN or internet connection is needed to use the completed store locally.

## 1. Run in XAMPP (Windows)

1. Install XAMPP with PHP 8.2 or newer and MariaDB/MySQL. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Extract this ZIP. Copy the inner `falcon-perfumes` folder into `C:\xampp\htdocs\`. The entry file must be `C:\xampp\htdocs\falcon-perfumes\index.php`, not a second nested folder.
3. Open `http://localhost/phpmyadmin`. Choose **Import**, select `C:\xampp\htdocs\falcon-perfumes\database.sql`, leave the format as **SQL**, and click **Import / Go**. The script creates `falcon_perfumes` and all tables with eight sample products. Import once into a fresh database; it is intentionally not a destructive reset script.
4. Default settings in `includes/config.php` use host `127.0.0.1`, port `3306`, database `falcon_perfumes`, user `root`, and a blank password (standard local XAMPP defaults). If yours differ, copy `includes/config.local.example.php` to `includes/config.local.php` and change your local settings there.
5. Open **http://localhost/falcon-perfumes/**. Do not double-click PHP files or use `file://` URLs.
6. Register a customer at **http://localhost/falcon-perfumes/auth/register.php**. No customer is pre-created.
7. Open **http://localhost/falcon-perfumes/admin/login.php**. Use the randomly generated temporary credentials in **ADMIN-LOGIN.txt**. You must set a new password on the first login.
8. In the admin area, edit product details, prices, stock and images. Place a test customer order, review it in Orders and change its status. Contact form submissions appear in Messages.

macOS XAMPP: use `/Applications/XAMPP/xamppfiles/htdocs/falcon-perfumes`. WAMP: use its `www/falcon-perfumes` directory and the equivalent Apache/MySQL controls. Keep `base_url` as `/falcon-perfumes` unless you rename the directory. If Apache uses port 8080, use `http://localhost:8080/falcon-perfumes/`.

### Database configuration example

`includes/config.local.php`:

```php
<?php
return [
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'falcon_perfumes',
    'db_user' => 'root',
    'db_password' => '',
    'base_url' => '/falcon-perfumes'
];
```

Never commit your real database password. `config.local.php` is excluded by `.gitignore`. The shipped blank root password is for local XAMPP only. For hosting, create a dedicated database user, configure HTTPS, change the admin credential and update `base_url` (use an empty string for the domain root). The supplied `.htaccess` rules target Apache 2.4; configure equivalent restrictions if using another web server. Keep docs, credentials and SQL files outside a public web root or preserve the provided access-denial rules.

### Image uploads

PHP extensions required: `pdo_mysql`, `mbstring`, `fileinfo` and sessions (standard XAMPP). Images must be JPG, PNG or WebP, at most 5 MB each and 20 megapixels, with 2–8 pictures per perfume. The admin creates random filenames after validating MIME type and image dimensions. Make `images/uploads` writable by Apache. To allow an eight-photo batch, set `upload_max_filesize=5M`, `post_max_size=45M` and `max_file_uploads=20` in XAMPP's `php.ini`, then restart Apache. A removed gallery photo is detached from the product; its file is retained for manual cleanup so deletion cannot break another reference.

## 2. Features

- Customer registration with unique username/email, bcrypt password hashing, login, session ID regeneration, clean POST logout and order history.
- Admin authentication uses its own `admins` table, cookie/session and permission guard on every protected admin route. Customer accounts cannot become admins through registration.
- Product create/edit/delete (recoverable archive), restore, multiple image uploads, cover selection and image removal. At least two photos must remain per product.
- Editable brands and categories, foreign-key protection against deleting a category/brand used by products.
- Stock quantities, low-stock warnings at five units or fewer, and an In Stock / Sold Out button. Zero stock also disables purchasing. Restock before switching a zero-stock product back to In Stock.
- Red SOLD OUT card badge, disabled grey button and server checks on cart and checkout.
- Product detail page at `product.php?id=...`, opened by selecting a card image or product name. It is not a standalone navigation menu item. Product details are public for browsing, not access restricted.
- Search with suggestions, collapsible filters, price sorting, database-backed pagination, brand browsing and related perfumes from the same brand.
- Server-side shopping bag, quantities, totals and cash-on-delivery checkout. Free delivery from USD 50; USD 5 below that threshold. Stock is locked and decremented atomically when an order is placed.
- Order status flow: Pending → Confirmed → Shipped → Delivered. Cancellation is allowed only from Pending or Confirmed and restores inventory once. Delivered and Cancelled are terminal states.
- Dashboard: total orders, delivered revenue, customer count, unread messages and low-stock products. Revenue includes delivery and counts only Delivered orders; this is a cash-on-delivery operational total, not a payment gateway settlement report.
- Registered customer list, order history and enable/disable controls. Disabled customers lose protected-page access on their next request.
- Contact form saves queries in `messages`, confirms success and allows admins to mark messages read/unread. It does not send email.
- Prepared PDO statements, output escaping, CSRF tokens, secure cookie options when HTTPS is used, basic login throttling (5 failures per identity + IP in 15 minutes), client validation and server validation.

## 3. Folder structure

```text
falcon-perfumes/
  css/style.css
  js/script.js
  images/products/             bundled matched perfume photos
  images/uploads/              admin uploads, execution denied
  includes/
    config.php                 default local settings
    config.local.example.php   configuration template
    db.php                     PDO connection/prepared-query helper
    functions.php              sessions, guards, CSRF, shared helpers
    header.php / footer.php    reusable frontend
  auth/register.php / login.php / logout.php
  admin/
    login.php / logout.php / password.php / _init.php
    index.php / products.php / product-edit.php / taxonomy.php
    orders.php / customers.php / messages.php
  index.php / new-arrivals.php / brands.php / contact.php
  product.php / cart.php / checkout.php / dashboard.php
  tools/reset-admin.php
  tests/smoke.php / TEST-RESULTS.md
  docs/                        report, screenshots, references, editable report source
  database.sql
  README.md
  ADMIN-LOGIN.txt               private temporary login; excluded from Git
  .htaccess / .gitignore
```

The four supplied pages referenced missing CSS and JavaScript. Those assets were recreated locally while retaining the navy/gold Falcon Perfumes theme, page roles, brand list and USD sample pricing. The application uses shared PHP templates rather than duplicating the old HTML. Original source pages are retained in `docs/original-frontend/` for comparison; they are references, not working store entry points.

## 4. Database design

| Table | Purpose and relationships |
|---|---|
| users | Customer accounts; unique username/email; bcrypt password; active flag; created timestamp |
| admins | Separate staff accounts; bcrypt password; first-login password-change flag |
| messages | Name, email, message, created timestamp, read/unread flag |
| brands | One brand to many products |
| categories | One category to many products |
| products | Name, description, three note stages, concentration, size, price, stock, sold-out/active flags; brand/category foreign keys |
| product_images | Many local image paths per product with display order |
| orders | Customer foreign key, delivery snapshot, totals, status, unique checkout token |
| order_items | Order/product foreign keys, product-name and unit-price snapshots, quantity |
| login_attempts | Hashed identity/IP key and time for basic throttling |

The SQL includes only the sample catalogue and a temporary admin password hash. No test customer, message or order is shipped. Decimal columns represent money; application totals are accumulated as integer cents. Product deletion is a soft archive to preserve historic order references.

## 5. Corrected perfume photos and names

Eight sample products: Afnan 9 PM, Lattafa Khamrah, Armaf Club de Nuit Intense Man EDT 105 ml, Rasasi Hawas for Him, Afnan Supremacy Not Only Intense Extrait 100 ml, Lattafa Asad, Maison Alhambra Jean Lowe Immortel and Lattafa Yara. Each has 2–3 locally bundled images. See `docs/IMAGE-CREDITS.md` and `docs/image-sources.json` for source URLs and precise changes to ambiguous original listings. Product prices and stock are demonstration values, not live stock or price quotations. Update them for your business. Brand imagery remains third-party copyrighted material.

## 6. Testing and troubleshooting

Tested using PHP 8.3 and MariaDB 10.11 with real HTTP requests and Chromium browser rendering. See `tests/TEST-RESULTS.md` for completed checks. The XAMPP UI itself was not run in this environment; complete the local setup check before submission.

Read-only smoke checks on Windows:

```bat
cd C:\xampp\htdocs\falcon-perfumes
C:\xampp\php\php.exe tests\smoke.php
```

- **Database connection error:** start MySQL, check its port/credentials and confirm `database.sql` was imported. The browser hides database credentials/errors; inspect Apache/PHP logs for details.
- **CSS/images missing:** check the `falcon-perfumes` folder is not nested twice and that `base_url` matches the folder name.
- **Cannot log in after import:** use `ADMIN-LOGIN.txt` for admin login; customer login requires registration. Clear cookies if you changed the base path. Check PHP's session save directory exists and is writable.
- **Upload rejected:** review file type, size and gallery limits; adjust the PHP limits described above; make sure `images/uploads` is writable.
- **Table already exists on import:** this script is for a fresh install. Back up any existing data and choose a fresh local database, updating the SQL database name and configuration together. Do not delete real data just to resolve an import error.
- **Forgotten admin password:** run `C:\xampp\php\php.exe tools\reset-admin.php falcon_admin` in the project folder. This local CLI-only tool prints a new random temporary password. It cannot run via a web request.

## 7. Report and final LMS submission

1. Open `docs/Falcon-Perfumes-Project-Report.pdf`. The report includes real screenshots from the tested application.
2. Fill in your actual project group members, index numbers, course details, contribution records and GitHub URL. These personal facts were not supplied and have not been invented. Use `docs/REPORT-CONTENT.json`, then run `python docs/build-report.py` with ReportLab installed to regenerate the report. Re-check the resulting PDF before submission.
3. Import the database locally and demonstrate registration, login, contact submission, a product gallery, stock changes and an order.
4. The included SQL is a clean MariaDB export produced after testing. For the literal phpMyAdmin export requirement: select `falcon_perfumes` in phpMyAdmin → **Export** → **Quick** → **SQL** → **Export**. Save that file as `database.sql` in your submission copy. Do not publish customer data or a private credential to a public repository. Use a clean catalogue-only database for public source code.
5. Create/update your GitHub repository using the steps below and paste its actual URL into the report and LMS. No GitHub repository was created or uploaded from this session.
6. Submit the ZIP, PDF and GitHub link on LMS. The brief says “Sunday, 7 September 2026”; 7 September 2026 is a Monday. Confirm the correct deadline with your lecturer/LMS.

## 8. GitHub instructions

No GitHub account or repository URL was provided. From the project directory, after installing Git:

```bash
git init
git add .
git status
git commit -m "Complete Falcon Perfumes PHP MySQL integration"
git branch -M main
```

Create an empty repository on GitHub, then use its actual URL:

```bash
git remote add origin https://github.com/YOUR-USERNAME/falcon-perfumes.git
git push -u origin main
```

Before committing, confirm `ADMIN-LOGIN.txt`, `includes/config.local.php`, live customer data and private credentials are not staged. The exported sample SQL contains a disposable demonstration admin hash, not your subsequently changed local password. Create a new admin with `tools/reset-admin.php` after cloning, rather than sharing a private password. If updating an existing repository, use that repository's normal branch/commit flow instead of adding a second origin.

## 9. Scope and future work

This submission is a local academic e-commerce demonstration. It has cash on delivery only: no card payment gateway, email sending, password-reset emails, two-factor authentication, refunds, tax engine or shipping-provider integration. Admin lists are suitable for the small project dataset; the public catalogue is paginated. Commercial deployment should add operational backup, monitoring and hosting-specific hardening. No business phone, real address or unsupported customer policies were invented.

## References

- PHP password_hash: https://www.php.net/manual/en/function.password-hash.php
- PHP password_verify: https://www.php.net/manual/en/function.password-verify.php
- PHP session_regenerate_id: https://www.php.net/manual/en/function.session-regenerate-id.php
- PDO prepared statements: https://www.php.net/manual/en/pdo.prepared-statements.php
- PHP file uploads: https://www.php.net/manual/en/features.file-upload.php
- MySQL InnoDB locking reads: https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html
- XAMPP: https://www.apachefriends.org/
- phpMyAdmin export: https://docs.phpmyadmin.net/en/latest/import_export.html
- Product references: `docs/IMAGE-CREDITS.md`.
