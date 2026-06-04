
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capture the form data securely
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password']; // To be hashed in week 4 when we implement real authentication
    
    // 3. Simple Form Processor Logic 
    
    if (!empty($email) && !empty($password)) {
        
        // Save the email into the global Session variable
        $_SESSION['manager_email'] = $email;
        $_SESSION['role'] = 'manager';
        
        // 4. Redirect them to the Dashboard/Catalog
        header("Location: index.php");
        exit();
    }
}
?>


    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decorum Bookshop.</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-bg">

    <div class="auth-card">
        <h2 class="brand-title">Decorum Bookshop</h2>
        <p class="subtitle">Inventory Management System</p>
        
        <form id="loginForm" action="login.php" method="POST">
            
            <div style="margin-bottom: 15px;">
                <label for="email" class="input-label">Email:</label>
                <input type="text" id="email" name="email" class="form-input">
                <span id="emailError" style="color: red; font-size: 12px; display: none; margin-top: 5px;">Format required: Must start with letters/numbers (no symbols), contain @, and end with .com</span>
            </div>

            <div style="margin-bottom: 25px;">
                <label for="password" class="input-label">Password:</label>
                <input type="password" id="password" name="password" class="form-input">
                <span id="passwordStrength" style="font-size: 12px; font-weight: bold; display: block; margin-top: 5px;"></span>
            </div>

            <button type="submit" class="primary-btn">
                Access Portal
            </button>
            
        </form>
    </div>

    <script src="js/script.js"></script>
</body>
</html>