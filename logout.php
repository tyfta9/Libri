<?php
    // start session if required
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    } 

    session_destroy();
    header("Location: login.php");
    exit();
?>