<?php 
    function connectDb(): mysqli {
        $servername = "localhost";
        $host_name = "root";
        $host_password = "";
        $dbname = "libri";

        // create connection to my database
        $conn = new mysqli($servername, $host_name, $host_password, $dbname);
        // check connection
        isConnected($conn);

        return $conn;
    }
    
    // check connection
    function isConnected(mysqli $conn): bool {
        // if connection failed, output error message and exit
        if($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return !$conn->connect_error;
    }

    // register user
    function registerUser($uname, $password, $mobile): string  {
        $conn = connectDb();

        // error message
        $message = "";

        // prepare sanitized input
        $safe_uname = $conn->real_escape_string($uname);
        $safe_password = $conn->real_escape_string($password);
        $safe_mobile = $conn->real_escape_string($mobile);
        
        // if a user with this name is already exists
        if(doesUserExist($safe_uname)) {
            // give error message and exit
            $message = "Username is already taken";
            $conn->close();
            return $message;
        }
        
        // insert sanitized data
        $sql = "INSERT INTO users (username, password, mobile) VALUES 
        ('$safe_uname', '$safe_password', '$safe_mobile');";

        // execute sql query
        $result = $conn->query($sql);

        // if there is an error
        if(!$result) {
            $message = "There was something wrong with your data";
            $conn->close();
            return $message;
        } 

        $message = fillInSession($safe_uname);
        
        $conn->close();

        // return the message regardless of it's content
        return $message;
    }

    function loginUser($uname, $password): string {
        $conn = connectDb();
        
        // prepare case insensitive and sanitized input
        $uname_lower = strtolower($uname);
        $safe_uname = $conn->real_escape_string($uname_lower);
        
        // get message from function
        $message = checkPassword($safe_uname, $password);

        // if message is empty, fill in the session info on the user
        if(!$message) {
            $message = fillInSession($safe_uname);
        }
        
        $conn->close();

        // return the message regardless of it's content
        return $message;
    }

    function fillInSession($safe_uname): string {
        $conn = connectDb();

        // error message
        $message = "";

        // sql query to get all user data
        $sql = "SELECT username, password, fname, sname, address1, address2, city, tel, mobile 
                FROM users WHERE username = '$safe_uname'";
        
        // query and store in result
        $result = $conn->query($sql);
        
        // if there is no result
        if(!$result) {
            $message = "Failed to check if user exists: " . $conn->error;
            $conn->close();
            return $message;
        }

        // user was not found
        if(!($result->num_rows > 0)) {
            $message = "User not found! from session";
            $conn->close();
            return $message;
        }

        $row = $result->fetch_assoc();
    
        // set all variables for session
        $_SESSION['username'] = $row['username'];
        $_SESSION['password'] = $row['password']; 
        $_SESSION['fname'] = $row['fname'];
        $_SESSION['sname'] = $row['sname'];
        $_SESSION['address1'] = $row['address1'];
        $_SESSION['address2'] = $row['address2'];
        $_SESSION['city'] = $row['city'];
        $_SESSION['tel'] = $row['tel'];
        $_SESSION['mobile'] = $row['mobile'];
        $_SESSION['logged_in'] = true;

        $conn->close();
        
        return $message;
    }

    function doesUserExist($safe_uname): bool {
        $conn = connectDb();
        
        // prepare case insensitive and sanitized input
        $uname_lower = strtolower($safe_uname);
        
        // execute sql query
        $sql = "SELECT username FROM users WHERE LOWER(username) = '$uname_lower'";
        $result = $conn->query($sql);
        
        // if result is false, die
        if (!$result) {
            $conn->close();
            die("Failed to check if user exists: " . $conn->error);
        }

        $conn->close();
        
        // return true if found user
        return $result->num_rows > 0;
    }

    function checkPassword($safe_uname, $password): string {
        $conn = connectDb();

        // error message init
        $message = "";
        
        // look for provided username
        $sql = "SELECT username, password FROM users WHERE LOWER(username) = '$safe_uname'";
        $result = $conn->query($sql);
        
        // if there is no result
        if(!$result) {
            $conn->close();
            die("Failed to check if user exists: " . $conn->error);
        }

        // if there is no rows / aka user is not found
        if(!($result->num_rows > 0)) {
            $message = "User not found!";
            $conn->close();
            return $message;
        }

        // take a row 
        $row = $result->fetch_assoc();

        // check password
        if($row["password"] !== $password) {
            $message = "Incorrect password!";
            $conn->close();
            return $message;
        }
        
        // if all was right and password matched, message should be empty
        $conn->close();
        return $message;
    }

    function reserveBook($book_isbn, $uname): string {
        $message = "";
        // if registration is not finished, output the message
        if(empty($_SESSION["city"]) || empty($_SESSION["fname"]) || empty($_SESSION["sname"]) || empty($_SESSION["address1"]) || empty($_SESSION["address2"]) || empty($_SESSION["tel"])) {
            $message = '<div class="error-message">Finish registration! -><a href="profile.php">My profile</a></div>';
            return $message;
        }
        
        $conn = connectDb();
        
        // sanitize input in case user could input value somehow
        $isbn = $conn->real_escape_string($book_isbn);
        $username = $conn->real_escape_string($uname);
        $date = date('d-M-Y');
        
        // check if the book is reserved
        $check_book = $conn->query("SELECT reserved FROM books WHERE isbn = '$isbn'");
        $book_data = $check_book->fetch_assoc();
        
        // if book is not reserved
        if($book_data && $book_data['reserved'] == false) {
            try {
                // reserve the book
                $insert_sql = "INSERT INTO reservedBooks (isbn, username, date) VALUES ('$isbn', '$username', '$date')";
                $conn->query($insert_sql);
                
                // update books status in database
                $update_sql = "UPDATE books SET reserved = true WHERE isbn = '$isbn'";
                $conn->query($update_sql);
                
                // output success massage
                $message = '<div class="success-message">Book reserved successfully!</div>';
                
            } catch (Exception $e) {
                // if exception is caught, output error message
                $message = '<div class="error-message">Failed to reserve book: ' . $e->getMessage() . '</div>';
            }
        } else {
            // error, the book is reserved
            $message = '<div class="error-message">This book is already reserved by someone else!</div>';
        }

        $conn->close();
        return $message;
    }
?>