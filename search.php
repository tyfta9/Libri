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
    $categories = [];
    $search_results = [];
    $search_message = '';
    $reserve_message = '';

    // get all categories for dropdown menu
    $category_result = $conn->query("SELECT * FROM categories ORDER BY description");
    while($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }

    // handle search
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // sanitize the input
        $title = $conn->real_escape_string($_POST['title'] ?? '');
        $author = $conn->real_escape_string($_POST['author'] ?? '');
        $category = $conn->real_escape_string($_POST['category'] ?? '');
        
        // compile a select query with user input
        $sql = "SELECT b.*, c.description as category_name FROM books b 
                LEFT JOIN categories c ON b.category = c.id 
                WHERE 1=1";
        
        // if value is not empty, include it in the search
        if(!empty($title)) {
            $sql .= " AND b.title LIKE '%$title%'";
        }
        
        if(!empty($author)) {
            $sql .= " AND b.author LIKE '%$author%'";
        }
        
        if(!empty($category)) {
            $sql .= " AND b.category = '$category'";
        }
        
        $sql .= " ORDER BY b.title";
        
        $result = $conn->query($sql);
        
        // if there are books found
        if($result->num_rows > 0) {
            // fetch them and put them into search result
            while($row = $result->fetch_assoc()) {
                $search_results[] = $row;
            }
        } else {
            $search_message = '<p class="no-results">No books found matching your search criteria.</p>';
        }
    }

    // handle reservation
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reserve_isbn'])) {
        $reserve_message = reserveBook($_POST["reserve_isbn"], $_SESSION["username"]);
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - Libri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="main-content">
        <div class="content-container">
            <h1 class="page-title">Search Books</h1>

            <!-- output message -->
            <?php
                if($reserve_message) {
                    echo $reserve_message;
                }
            ?>
            
            <div class="search-section">
                <!-- search form -->
                <form method="post" class="search-form">
                    <div class="form-group">
                        <label for="title" class="form-label">Book Title:</label>
                        <input type="text" id="title" name="title" class="form-input" 
                               placeholder="Enter full or partial title" 
                               value="<?php echo htmlentities($_POST['title'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="author" class="form-label">Author:</label>
                        <input type="text" id="author" name="author" class="form-input" 
                               placeholder="Enter full or partial author name"
                               value="<?php echo htmlentities($_POST['author'] ?? ''); ?>">
                    </div>
                    
                    <!-- drop down menu for categories -->
                    <div class="form-group">
                        <label for="category" class="form-label">Category:</label>
                        <select id="category" name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat) {?>
                                <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo (isset($_POST['category']) && $_POST['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlentities($cat['description']); ?>
                                </option>
                            <?php }?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" value="Search" class="submit-btn">
                        <button type="reset" class="reset-btn">Clear</button>
                    </div>
                </form>
            </div>
            
            <!-- search message -->
            <?php echo $search_message; ?>
            
            <!-- output books -->
            <?php if(!empty($search_results)) {?>
                <div class="results-section">
                    <h2 class="results-title">Search Results (<?php echo count($search_results); ?> found)</h2>
                    
                    <div class="results-grid">
                        <?php foreach($search_results as $book) {?>
                            <div class="book-card <?php echo $book['reserved'] ? 'reserved' : 'available'; ?>">
                                <div class="book-header">
                                    <h3 class="book-title"><?php echo htmlentities($book['title']); ?></h3>
                                    <div class="book-status">
                                        <span class="status-badge <?php echo $book['reserved'] ? 'status-reserved' : 'status-available'; ?>">
                                            <?php echo $book['reserved'] ? 'Reserved' : 'Available'; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="book-details">
                                    <p class="book-author"><strong>Author:</strong> <?php echo htmlentities($book['author']); ?></p>
                                    <p class="book-category"><strong>Category:</strong> <?php echo htmlentities($book['category_name']); ?></p>
                                    <p class="book-isbn"><strong>ISBN:</strong> <?php echo htmlentities($book['isbn']); ?></p>
                                    <p class="book-year"><strong>Year:</strong> <?php echo $book['year']; ?></p>
                                    <p class="book-edition"><strong>Edition:</strong> <?php echo $book['edition']; ?></p>
                                </div>
                                
                                <!-- form to reserve the book if it is available -->
                                <?php if(!$book['reserved']) {?>
                                    <form method="post" class="reserve-form">
                                        <input type="hidden" name="reserve_isbn" value="<?php echo $book['isbn']; ?>">
                                        <input type="hidden" name="title" value="<?php echo htmlentities($_POST['title'] ?? ''); ?>">
                                        <input type="hidden" name="author" value="<?php echo htmlentities($_POST['author'] ?? ''); ?>">
                                        <input type="hidden" name="category" value="<?php echo htmlentities($_POST['category'] ?? ''); ?>">
                                        <button type="submit" class="reserve-btn">Reserve This Book</button>
                                    </form>
                                <?php } else {?>
                                    <div class="reserved-info">
                                        <p class="reserved-text">This book is already reserved</p>
                                    </div>
                                <?php }?>
                            </div>
                        <?php }?>
                    </div>
                </div>
            <?php } elseif($_SERVER["REQUEST_METHOD"] == "POST" && empty($search_message)) {?>
                <div class="no-results-message">
                    <p>No books found matching your search criteria.</p>
                </div>
            <?php }?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>