<?php
session_start();
if (!isset($_SESSION['manager_email'])) {
    header("Location: login.php");
    exit();
}
$user_email = $_SESSION['manager_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decorum Bookshop - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard-body">

    <div class="sidebar" id="sidebarMenu">
        <h2 class="brand-title">Decorum B2B</h2>
        <ul class="nav-list">
            <li class="nav-item"><a href="#" class="nav-link">Catalog</a></li>
            <li class="nav-item"><a href="#" class="nav-link">My Orders</a></li>
            <li class="nav-item"><a href="profile.php" class="nav-link">Profile</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link danger">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <button id="toggleMenuBtn" class="primary-btn" style="width: auto; margin-bottom: 20px;">Toggle Sidebar</button>
        
        <div class="auth-card" style="max-width: none;">
            <h1 style="color: #333;">Welcome to the Portal!</h1>
            <p style="font-size: 18px;">
                You are securely logged in as: 
                <strong style="color: blue;"><?php echo $user_email; ?></strong>
            </p>
        </div>
    </div>

    <script src="js/dashboard.js"></script>
</body>
</html>