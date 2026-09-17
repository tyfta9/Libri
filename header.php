<?php
    // start session if required
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libri - Library System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <div class="header-container">
            <h1 class="site-title">Libri - Library System</h1>
            <nav class="main-nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="books.php" class="nav-link">Browse Books</a></li>
                    <li class="nav-item"><a href="reserved.php" class="nav-link">My Reservations</a></li>
                    <li class="nav-item"><a href="profile.php" class="nav-link">My Profile</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
                </ul>
            </nav>
            <div class="user-info">
                <span class="welcome-message">Welcome, <?php echo $_SESSION['username'] ?? 'Guest'; ?></span>
            </div>
        </div>
    </header>