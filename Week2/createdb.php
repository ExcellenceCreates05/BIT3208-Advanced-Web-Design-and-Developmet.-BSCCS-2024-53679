<?php
$servername = "localhost"; 
$username = "root";
$password = "";

try {
    // 1. Connect to the MySQL server (Notice we leave out 'dbname' because it doesn't exist yet!)
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Store your SQL command in a variable
    $sql = "CREATE DATABASE IF NOT EXISTS decorum_bookshop";
    
    // 3. Use the PDO exec() method to run the SQL command
    $conn->exec($sql);
    
    echo "Success! The Decorum Bookshop database was created programmatically.";
    
} catch(PDOException $e) {
    echo "Creation failed: " . $e->getMessage();
}
?>