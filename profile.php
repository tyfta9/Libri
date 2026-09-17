<?php
    require_once 'functions.php';

    // start session if required
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // if session is on but user is not logged_in
    if(!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] === FALSE) {
        // redirect to login page and exit
        session_destroy();
        header("Location: login.php");
        exit();
    }

    $message = '';

    // if the form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = connectDb();
        
        // sanitize input
        $fname = $conn->real_escape_string($_POST['fname']);
        $sname = $conn->real_escape_string($_POST['sname']);
        $address1 = $conn->real_escape_string($_POST['address1']);
        $address2 = $conn->real_escape_string($_POST['address2']);
        $city = $conn->real_escape_string($_POST['city']);
        $tel = $conn->real_escape_string($_POST['tel']);
        $username = $_SESSION['username'];
        
        // update database values for the user
        $sql = "UPDATE users SET 
                fname = '$fname',
                sname = '$sname',
                address1 = '$address1',
                address2 = '$address2',
                city = '$city',
                tel = '$tel'
                WHERE username = '$username'";
        
        // if the query was successful
        if($conn->query(query: $sql)) {
            $message = '<div class="success-message">Profile updated successfully!</div>';
            // update the rest of session values for the user
            $_SESSION['fname'] = $fname;
            $_SESSION['sname'] = $sname;
            $_SESSION['address1'] = $address1;
            $_SESSION['address2'] = $address2;
            $_SESSION['city'] = $city;
            $_SESSION['tel'] = $tel;
        } else {
            $message = '<div class="error-message">Error updating profile: ' . $conn->error . '</div>';
        }
        
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Libri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="main-content">
        <div class="content-container">
            <h1 class="page-title">My Profile</h1>
            
            <?php
                // if the message is not empty, output it
                if($message) {
                    echo $message; 
                }
            ?>
           
            <!-- output form to finish registration -->
            <div class="profile-section">
                <form method="post" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fname" class="form-label">First Name:</label>
                            <!-- if the first name is in session, sanitize it and put it in -->
                            <input type="text" id="fname" name="fname" class="form-input" 
                                   value="<?php echo htmlentities($_SESSION['fname'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sname" class="form-label">Last Name:</label>
                            <!-- if the second name is in session, sanitize it and put it in -->
                            <input type="text" id="sname" name="sname" class="form-input" 
                                   value="<?php echo htmlentities($_SESSION['sname'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address1" class="form-label">Address Line 1:</label>
                        <!-- if the address1 is in session, sanitize it and put it in -->
                        <input type="text" id="address1" name="address1" class="form-input" 
                               value="<?php echo htmlentities($_SESSION['address1'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address2" class="form-label">Address Line 2:</label>
                        <!-- if the address2 is in session, sanitize it and put it in -->
                        <input type="text" id="address2" name="address2" class="form-input" 
                               value="<?php echo htmlentities($_SESSION['address2'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city" class="form-label">City:</label>
                            <!-- if the city is in session, sanitize it and put it in -->
                            <input type="text" id="city" name="city" class="form-input" 
                                   value="<?php echo htmlentities($_SESSION['city'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tel" class="form-label">Telephone:</label>
                            <!-- if the telephone is in session, sanitize it and put it in -->
                            <input type="text" id="tel" name="tel" class="form-input" 
                                   value="<?php echo htmlentities($_SESSION['tel'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="readonly-info">
                        <div class="info-item">
                            <span class="info-label">Username:</span>
                            <!-- sanitize before output -->
                            <span class="info-value"><?php echo htmlentities($_SESSION['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Mobile:</span>
                            <span class="info-value"><?php echo htmlentities($_SESSION['mobile']); ?></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" value="Update Profile" class="submit-btn">
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>