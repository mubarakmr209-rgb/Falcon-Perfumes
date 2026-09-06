<?php
declare(strict_types=1);

/**
 * Returns the shared PDO connection for the Falcon Perfumes MySQL database.
 * Change these values only if your XAMPP/WAMP MySQL setup uses different credentials.
 */
function db(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = 'localhost';
    $database = 'falcon_perfumes';
    $username = 'root';
    $password = '';
    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

    try {
        $connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        error_log('Falcon Perfumes database connection failed: ' . $exception->getMessage());
        http_response_code(500);
        exit('Database connection failed. Import database.sql and check includes/db.php.');
    }

    return $connection;
}
