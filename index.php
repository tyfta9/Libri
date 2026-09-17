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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Libri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="main-content">
        <div class="content-container">
            <section class="welcome-section">
                <h2 class="section-title">Welcome to Libri - Library System</h2>
                <p class="welcome-text">You have successfully logged in!</p>
            </section>
            
            <section class="quick-actions">
                <h3 class="actions-title">Quick Actions</h3>
                <div class="actions-grid">
                    <a href="books.php" class="action-card">
                        <h4 class="card-title">Browse Books</h4>
                        <p class="card-text">View all available books</p>
                    </a>
                    
                    <a href="search.php" class="action-card">
                        <h4 class="card-title">Search Books</h4>
                        <p class="card-text">Advanced search by title, author, category</p>
                    </a>
                    
                    <a href="reserved.php" class="action-card">
                        <h4 class="card-title">My Reservations</h4>
                        <p class="card-text">View your reserved books</p>
                    </a>
                    
                    <a href="profile.php" class="action-card">
                        <h4 class="card-title">My Profile</h4>
                        <p class="card-text">Update your information</p>
                    </a>
                </div>
            </section>
            
            <section class="stats-section">
                <h3 class="stats-title">Library Statistics</h3>
                <div class="stats-container">
                    <?php
                        $conn = connectDb();
                        // count total books in db
                        $total_books = $conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
                        // count total categories in db
                        $total_categories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
                        // count all reserved books by the user in db
                        $reserved_books = $conn->query("SELECT COUNT(*) as total FROM reservedBooks WHERE username = '".$_SESSION['username']."'")->fetch_assoc()['total'];
                        // count all available books in db
                        $available_books = $conn->query("SELECT COUNT(*) as total FROM books WHERE reserved = false")->fetch_assoc()['total'];
                        $conn->close();
                    ?>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_books; ?></span>
                        <span class="stat-label">Total Books</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_categories; ?></span>
                        <span class="stat-label">Categories</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $available_books; ?></span>
                        <span class="stat-label">Available Books</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $reserved_books; ?></span>
                        <span class="stat-label">Your Reservations</span>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>