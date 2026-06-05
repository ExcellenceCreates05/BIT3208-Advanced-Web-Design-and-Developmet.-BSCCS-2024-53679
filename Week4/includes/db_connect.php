<?php
/**
 * =============================================================
 * DECORUM BOOKSHOP — B2B INVENTORY SYSTEM
 * includes/db_connect.php — Secure Database Connection
 * Week 3: Basic database connection practice
 * =============================================================
 *
 * WHY PDO INSTEAD OF mysqli_connect()?
 *   - PDO (PHP Data Objects) works with multiple database types
 *   - PDO forces you to use Prepared Statements which prevents
 *     SQL Injection attacks — a critical security practice
 *   - More industry-relevant than raw mysqli functions
 *
 * HOW THIS WORKS:
 *   Every other PHP file that needs the database does:
 *     require_once __DIR__ . '/db_connect.php';
 *   Then uses $pdo to run queries.
 * =============================================================
 */

// --- Database Configuration ---
// Change these to match your XAMPP setup
define('DB_HOST', 'localhost');
define('DB_NAME', 'decorum_bookshop');   // We create this in Week 3
define('DB_USER', 'root');               // XAMPP default user
define('DB_PASS', '');                   // XAMPP default is empty password
define('DB_CHARSET', 'utf8mb4');

// --- Build the DSN (Data Source Name) ---
// DSN tells PDO: which driver to use, which host, which database
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// --- PDO Options ---
// These make PDO behave safely and throw proper errors
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on DB errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return rows as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Use real prepared statements
];

// --- Create the PDO Connection ---
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    // If we reach here, connection was successful
    // No echo here — this file is included silently

} catch (PDOException $e) {
    /**
     * IMPORTANT: In production, NEVER show the raw error to users.
     * Here we show it for development/learning purposes.
     * In production you would: error_log($e->getMessage()); and show a friendly page.
     */
    error_log('[DB ERROR] ' . $e->getMessage()); // Log to server error log
    die(json_encode([
        'error' => true,
        'message' => 'Database connection failed. Please check your XAMPP MySQL server is running.'
    ]));
}

/*
 * HOW TO USE $pdo IN OTHER FILES:
 * ================================
 *
 * SIMPLE SELECT (fetch all):
 *   $stmt = $pdo->query("SELECT * FROM books");
 *   $books = $stmt->fetchAll();
 *
 * PREPARED STATEMENT (safe way with user input):
 *   $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
 *   $stmt->execute(['id' => $id]);
 *   $book = $stmt->fetch();
 *
 * INSERT:
 *   $stmt = $pdo->prepare("INSERT INTO books (title, author) VALUES (:title, :author)");
 *   $stmt->execute(['title' => $title, 'author' => $author]);
 *
 * UPDATE:
 *   $stmt = $pdo->prepare("UPDATE books SET title = :title WHERE id = :id");
 *   $stmt->execute(['title' => $title, 'id' => $id]);
 *
 * DELETE:
 *   $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
 *   $stmt->execute(['id' => $id]);
 */
?>
