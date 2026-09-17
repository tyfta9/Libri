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
    $books = [];
    $categories = [];

    // run sql query to retrieve books categories
    $category_result = $conn->query("SELECT * FROM categories ORDER BY description");
    while($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }

    // run the sql query and retrieve books table 
    $sql = "SELECT b.*, c.description as category_name FROM books b 
            LEFT JOIN categories c ON b.category = c.id 
            ORDER BY b.title";
    $book_result = $conn->query($sql);
    while($row = $book_result->fetch_assoc()) {
        $books[] = $row;
    }

    // handle reservation
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reserve_isbn'])) {
        $message = reserveBook($_POST["reserve_isbn"], $_SESSION["username"]);
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - Libri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="main-content">
        <div class="content-container">
            <h1 class="page-title">Browse All Books</h1>
            
            <div class="search-container">
                <button class="search-button"><a href="search.php" class="search-link">Advanced Search →</a></button>
            </div>
            
            <!-- output message -->
            <?php if(isset($message)) {?>
                <?php echo $message; ?>
            <?php } ?>
            
            <div class="books-section">
                <div class="books-grid">
                    <!-- go through each book and echo it's values -->
                    <?php foreach($books as $book) {?>
                        <div class="book-card <?php echo $book['reserved'] ? 'reserved' : 'available'; ?>">
                            <div class="book-header">
                                <!-- echo header of the book -->
                                <h3 class="book-title"><?php echo $book['title']; ?></h3>
                                <div class="book-status">
                                    <span class="status-badge <?php echo $book['reserved'] ? 'status-reserved' : 'status-available'; ?>">
                                        <?php echo $book['reserved'] ? 'Reserved' : 'Available'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- echo values -->
                            <div class="book-details">
                                <p class="book-author"><strong>Author:</strong> <?php echo $book['author']; ?></p>
                                <p class="book-category"><strong>Category:</strong> <?php echo $book['category_name']; ?></p>
                                <p class="book-isbn"><strong>ISBN:</strong> <?php echo $book['isbn']; ?></p>
                                <p class="book-year"><strong>Year:</strong> <?php echo $book['year']; ?></p>
                                <p class="book-edition"><strong>Edition:</strong> <?php echo $book['edition']; ?></p>
                            </div>
                            
                            <!-- show form to reserve the book if available -->
                            <?php if(!$book['reserved']) {?>
                                <form method="post" class="reserve-form">
                                    <input type="hidden" name="reserve_isbn" value="<?php echo $book['isbn']; ?>">
                                    <button type="submit" class="reserve-btn">Reserve This Book</button>
                                </form>
                            <!-- otherwise show that the book is reserved -->
                            <?php } else {?>
                                <div class="reserved-info">
                                    <p class="reserved-text">This book is already reserved</p>
                                </div>
                            <?php }?>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>