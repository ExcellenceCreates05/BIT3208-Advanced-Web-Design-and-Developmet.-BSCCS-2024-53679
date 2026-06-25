<?php

$host = "localhost";
$dbname = "decorum_bookshop"; 
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is blank


$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Create a new PDO instance to connect to the database
    $pdo = new PDO($dsn, $username, $password);
    
    // Set PDO to throw exceptions if there is an SQL error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Syllabus Requirement: Display success message
    echo "<h3 style='color: green; text-align: center; margin-top: 50px;'>Database Connected Successfully</h3>";
    
} catch (PDOException $e) {

    echo "<h3 style='color: red; text-align: center; margin-top: 50px;'>Connection Failed: " . $e->getMessage() . "</h3>";
}
?>