<?php
/**
 * Database connection (PDO)
 * ---------------------------------------------------------------
 * Edit the constants below to match your local WAMP/XAMPP setup.
 * Default XAMPP/WAMP MySQL credentials are usually:
 *   host: 127.0.0.1
 *   user: root
 *   pass: (empty)
 */

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'ssms_uganda');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function get_db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, log this instead of echoing it.
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    return $pdo;
}
