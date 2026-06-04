<?php

$host = "localhost";
$dbname = "decorum_bookshop"; 
$username = "root"; 
$password = ""; 


$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    
    $pdo = new PDO($dsn, $username, $password);
    
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    echo "<h3 style='color: green; text-align: center; margin-top: 50px;'>Database Connected Successfully</h3>";
    
} catch (PDOException $e) {

    echo "<h3 style='color: red; text-align: center; margin-top: 50px;'>Connection Failed: " . $e->getMessage() . "</h3>";
}
?>