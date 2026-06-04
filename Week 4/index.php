<?php

session_start();


if (!isset($_SESSION['manager_email'])) {
    // 2. If no, redirect to the login page
    header("Location: login.php");
    exit();
} else {
    // 3. session DOES exist, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>

