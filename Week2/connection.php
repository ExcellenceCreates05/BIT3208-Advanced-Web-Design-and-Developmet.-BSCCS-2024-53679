<?php
$servername = "localhost";  
$username = "root";
$password = "";
try {
    $conn = new PDO("mysql:host=$servername;dbname=decorum_bookshop", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully<br>"; 
    echo "Yay! Congratulation!"; 
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}
 ?>