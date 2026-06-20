<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'decorum_bookshop');   
define('DB_USER', 'root');               
define('DB_PASS', '');                   
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;


$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        
    PDO::ATTR_EMULATE_PREPARES   => false,                   
];

//Create the PDO Connection
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    

} catch (PDOException $e) {
   
    error_log('[DB ERROR] ' . $e->getMessage()); // Log to server error log
    die(json_encode([
        'error' => true,
        'message' => 'Database connection failed. Please check your XAMPP MySQL server is running.'
    ]));
}

