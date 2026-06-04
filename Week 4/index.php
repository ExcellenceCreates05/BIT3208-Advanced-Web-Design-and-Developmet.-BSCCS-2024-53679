<?php

session_start();


if (!isset($_SESSION['manager_email'])) {
    header("Location: login.php");
    exit();
}

// Store the email from the session into a variable
$user_email = $_SESSION['manager_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decorum Bookshop - Dashboard</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; margin: 0; display: flex;}
        .sidebar { width: 250px; background-color: white; height: 100vh; padding: 20px; border-right: 1px solid #ccc; transition: 0.3s;}
        .content { padding: 40px; flex-grow: 1; }
        .hidden { display: none; } /* For DOM Manipulation */
    </style>
</head>
<body>

    <div class="sidebar" id="sidebarMenu">
        <h2 style="color: blue;">Decorum Bookshop</h2>
        <ul style="list-style-type: none; padding: 0;">
            <li style="margin-bottom: 15px;"><a href="#" style="text-decoration: none; color: gray;">Catalog</a></li>
            <li style="margin-bottom: 15px;"><a href="#" style="text-decoration: none; color: gray;">My Orders</a></li>
            <li style="margin-bottom: 15px;"><a href="profile.php" style="text-decoration: none; color: gray;">Profile</a></li>
            <li><a href="logout.php" style="color: red; text-decoration: none;">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <button id="toggleMenuBtn" style="padding: 10px; background: blue; color: white; border: none; cursor: pointer; margin-bottom: 20px;">Hide Sidebar</button>
        
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <h1 style="color: #333;">Welcome to Decorum Bookshop</h1>
            <p style="font-size: 18px;">
                You are securely logged in as: 
                <strong style="color: blue;"><?php echo $user_email; ?></strong>
            </p>
        </div>
    </div>
    <script src="js/dashboard.js"></script>

</body>
</html>