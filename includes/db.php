<?php
// ============================================================
// ILLUME — Database Connection (PDO Singleton)
// ============================================================

require_once __DIR__ . '/../config/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function connect(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                if (DEBUG) {
                    die('<pre style="background:#020207;color:#ff4c4c;padding:2rem;font-family:monospace;">
DB Connection Failed: ' . htmlspecialchars($e->getMessage()) . '
</pre>');
                }
                die('A database error occurred. Please try again later.');
            }
        }
        return self::$instance;
    }

    // Prevent instantiation
    private function __construct() {}
    private function __clone() {}
}

// Shorthand helper
function db(): PDO {
    return Database::connect();
}
