<?php
    require_once 'functions.php';

    // start session if required
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // if logged in
    if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === TRUE) {
        // redirect to main page
        header("Location: index.php");
        exit();
    }

    $message = "";
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        if(empty($username) || empty($password)) {
            $message = '<div class="error-message">Please fill in all fields</div>';
        } else {
            $message = loginUser($username, $password);
            if(empty($message)) {
                header("Location: index.php");
                exit();
            } else {
                $message = '<div class="error-message">' . $message . '</div>';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Libri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-form-container">
        <h1 class="auth-title">Login to Libri</h1>
        
        <?php 
            // if message is not empty, echo it
            if($message) {
                echo $message; 
            }
        ?>
        
        <!-- login form -->
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="username" class="form-label">Username:</label>
                <!-- if the username was set, put it in back -->
                <input type="text" id="username" name="username" class="form-input" 
                        value="<?php echo isset($_POST['username']) ? htmlentities($_POST['username']) : ''; ?>" 
                        required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Login" class="submit-btn">
            </div>
        </form>
        
        <div class="auth-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>