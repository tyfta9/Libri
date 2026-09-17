<?php
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

    // if the form is submitted
    if(isset($_POST["name"]) && isset($_POST["mobile"]) 
    && isset($_POST["pw"]) && isset($_POST["cpw"])) {
        // if the passwords do not match
        if($_POST["pw"] !== $_POST["cpw"]) {
            $message = "<div class='error-message'>Passwords do not match!</div>";
        } else {
            require_once "functions.php";
            $message = registerUser($_POST["name"],$_POST["pw"],$_POST["mobile"]);
            
            // if there is no error
            if(empty($message) && isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === TRUE) {
                header("Location: index.php");
                exit();
            } else {
                $message = "<div class='error-message'>$message</div>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Libri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-form-container">
        <h1 class="auth-title">Create Account</h1>
        
        <!-- error output -->
        <?php 
            if($message) {
                echo $message;
            }
        ?>
        
        <form action="register.php" method="post" autocomplete="on" id="registerForm">
            <div class="form-group">
                <label for="name" class="form-label">Username:</label>
                <!-- input sanitized post value if set -->
                <input type="text" id="name" name="name" class="form-input"
                        value="<?php echo isset($_POST['name']) ? htmlentities($_POST['name']) : ''; ?>" 
                        required>
            </div>
            
            <div class="form-group">
                <label for="mobile" class="form-label">Mobile Phone:</label>
                <!-- input sanitized post value if set -->
                <input type="tel" id="mobile" name="mobile" class="form-input"
                        value="<?php echo isset($_POST['mobile']) ? htmlentities($_POST['mobile']) : ''; ?>" 
                        required pattern="[\d]{10}" 
                        title="Must be 10 digits">
                <div class="password-requirements">Format: 10 digits (e.g., 1234567890)</div>
            </div>
            
            <div class="form-group">
                <label for="pw" class="form-label">Password:</label>
                <input type="password" id="pw" name="pw" class="form-input"
                        required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}"
                        title="Must contain at least one number, one uppercase and lowercase letter, and at least 6 characters">
                <div class="password-requirements">Must contain: 6+ characters, uppercase & lowercase letters, and a number</div>
            </div>
            
            <div class="form-group">
                <label for="cpw" class="form-label">Confirm Password:</label>
                <input type="password" id="cpw" name="cpw" class="form-input"
                        required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}">
                <div id="passwordMatchMessage" class="password-mismatch"></div>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Register" class="submit-btn" id="submitBtn">
            </div>
        </form>
        
        <div class="auth-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>