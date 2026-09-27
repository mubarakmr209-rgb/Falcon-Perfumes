# Falcon Perfumes - Phase 3 PHP & MySQL Integration

Falcon Perfumes is a fragrance catalogue website enhanced with PHP, MySQL, secure customer authentication, and a database-backed contact form for ICT 1209 Web Technologies.

## Features

- Customer registration using `password_hash($password, PASSWORD_BCRYPT)`.
- Secure login with `password_verify()`, PHP sessions, and `session_regenerate_id(true)` after login.
- Secure logout that clears and destroys the session.
- Contact form with client-side JavaScript validation plus server-side validation.
- PDO prepared statements for every database operation that accepts user input.
- CSRF tokens on registration, login, and contact forms.
- Theme-specific `perfumes` catalogue table shown on the protected dashboard.

## Required software

- XAMPP or WAMP with Apache and MySQL running.
- PHP 8.1+ with `pdo_mysql` enabled.

## Setup in XAMPP/WAMP

1. Start **Apache** and **MySQL** in the XAMPP/WAMP control panel.
2. Copy this folder into the web root (for XAMPP: `htdocs`; for WAMP: `www`).
3. Open `http://localhost/phpmyadmin` and choose **Import**.
4. Select `database.sql` from this project and click **Import**. It creates the `falcon_perfumes` database and all required tables.
5. Confirm the database credentials in `includes/db.php`. The default XAMPP credentials are host `localhost`, user `root`, and an empty password.
6. Browse to `http://localhost/falcon-perfumes-submission/index.php` (adjust the folder name if you renamed it).
7. Create an account, log in, visit the dashboard, and submit a test contact message. Verify records through phpMyAdmin if needed.

## Folder structure

```text
falcon-perfumes-submission/
├── auth/                 # register.php, login.php, logout.php
├── css/                  # existing Falcon Perfumes styles
├── images/               # image asset folder
├── includes/             # db.php and reusable functions.php
├── js/                   # existing validation and UI scripts
├── contact.php           # database-backed contact form
├── dashboard.php         # protected customer page and catalogue query
├── index.php             # home page
├── database.sql          # MySQL database export
└── README.md             # setup guide
```

## Database schema

| Table | Purpose |
| --- | --- |
| `users` | Registered customer credentials and account creation date. |
| `messages` | Contact-form submissions. |
| `perfumes` | Falcon Perfumes product catalogue. |
