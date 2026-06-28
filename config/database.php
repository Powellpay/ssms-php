<?php
// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — Database Configuration
//  Edit the four constants below to match your server.
// ─────────────────────────────────────────────────────────────────────────────

define('DB_HOST', 'localhost');
define('DB_NAME', 'ssms_uganda');
define('DB_USER', 'root');        // change to your MySQL username
define('DB_PASS', '');            // change to your MySQL password
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a singleton PDO connection.
 * Call db() anywhere to get the database handle.
 */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // In production, log the error — never echo the message to the browser.
            error_log('DB connection failed: ' . $e->getMessage());
            die(json_encode(['error' => 'Database connection failed. Check config/database.php.']));
        }
    }
    return $pdo;
}
