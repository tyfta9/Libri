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

    $conn = connectDb();
    $reserved_books = [];

    // select users reserved books from db
    $sql = "SELECT b.*, rb.date as reservation_date, c.description as category_name 
            FROM reservedBooks rb 
            JOIN books b ON rb.isbn = b.isbn 
            LEFT JOIN categories c ON b.category = c.id 
            WHERE rb.username = '".$_SESSION['username']."' 
            ORDER BY rb.date DESC";
    $result = $conn->query($sql);

    // fetch all rows to books array
    while($row = $result->fetch_assoc()) {
        $reserved_books[] = $row;
    }

    // handle remove reservation
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_isbn'])) {
        $isbn = $conn->real_escape_string($_POST['remove_isbn']);
        $username = $conn->real_escape_string($_SESSION['username']);
        
        try {
            // remove from reservedBooks
            $delete_sql = "DELETE FROM reservedBooks WHERE isbn = '$isbn' AND username = '$username'";
            $conn->query($delete_sql);
            
            // update book reserved status
            $update_sql = "UPDATE books SET reserved = false WHERE isbn = '$isbn'";
            $conn->query($update_sql);
            
            // refresh the page
            header("Location: reserved.php");
            exit();
            
        } catch (Exception $e) {
            $error_message = '<div class="error-message">Failed to remove reservation: ' . $e->getMessage() . '</div>';
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Libri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="main-content">
        <div class="content-container">
            <h1 class="page-title">My Reservations</h1>
            
            <?php if(isset($error_message)): ?>
                <?php echo $error_message; ?>
            <?php endif; ?>
            
            <?php if(empty($reserved_books)): ?>
                <div class="empty-state">
                    <p class="empty-message">You have no reserved books.</p>
                    <a href="books.php" class="browse-link">Browse Books</a>
                    <a href="search.php" class="browse-link">Search Books</a>
                </div>
            <?php else: ?>
                <div class="reservations-table">
                    <table class="data-table">
                        <thead>
                            <tr class="table-header">
                                <th class="table-cell">Title</th>
                                <th class="table-cell">Author</th>
                                <th class="table-cell">ISBN</th>
                                <th class="table-cell">Category</th>
                                <th class="table-cell">Reservation Date</th>
                                <th class="table-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($reserved_books as $book): ?>
                                <tr class="table-row">
                                    <td class="table-cell"><?php echo htmlentities($book['title']); ?></td>
                                    <td class="table-cell"><?php echo htmlentities($book['author']); ?></td>
                                    <td class="table-cell"><?php echo htmlentities($book['isbn']); ?></td>
                                    <td class="table-cell"><?php echo htmlentities($book['category_name']); ?></td>
                                    <td class="table-cell"><?php echo htmlentities($book['reservation_date']); ?></td>
                                    <td class="table-cell">
                                        <form method="post" class="remove-form" onsubmit="return confirm('Are you sure you want to remove this reservation?');">
                                            <input type="hidden" name="remove_isbn" value="<?php echo $book['isbn']; ?>">
                                            <button type="submit" class="remove-btn">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="reservation-count">
                    <p class="count-text">You have <?php echo count($reserved_books); ?> reserved book(s).</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>