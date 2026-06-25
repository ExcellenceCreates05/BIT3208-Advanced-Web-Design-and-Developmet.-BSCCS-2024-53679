
<?php
// login.php
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
    <title>Decorum Bookshop Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: #f4f4f9; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">

    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 400px;">
        <h2 style="color: blue; text-align: center; margin-bottom: 5px;">Decorum Bookshop</h2>
        <p style="text-align: center; color: gray; margin-bottom: 20px;">Inventory Management System</p>
        
        <form id="loginForm" action="login.php" method="POST">
            
            <div style="margin-bottom: 15px;">
                <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Enter Email:</label>
                <input type="text" id="email" name="email" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <span id="emailError" style="color: red; font-size: 12px; display: none; margin-top: 5px;">Please enter a valid email address containing an '@' symbol.</span>
            </div>

            <div style="margin-bottom: 25px;">
                <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">Password:</label>
                <input type="password" id="password" name="password" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <span id="passwordStrength" style="font-size: 12px; font-weight: bold; display: block; margin-top: 5px;"></span>
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background-color: blue; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
                Access Portal
            </button>
            
        </form>
    </div>

    <script src="js/script.js"></script>
</body>
</html>