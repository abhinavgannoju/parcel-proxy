<?php
session_start();

$DB_USERNAME = "root";  
$DB_PASS = "";         
$DB_HOSTNAME = "localhost:3306";
$DB_NAME = "project";

// Establish the database connection
$conn = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASS, $DB_NAME);

if (!$conn) {
    die('DATABASE CONNECTION ERROR: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use a prepared statement to avoid SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($result = mysqli_fetch_assoc($res)) {
        // Verify password (assuming it's hashed in the database)
        if (password_verify($password, $result['password'])) {
            // Store email and username in session
            $_SESSION['email'] = $result['email'];  
            $_SESSION['username'] = $result['username']; // Store username in session
            header('Location: main_page.php');      // Redirect to main page
            exit();
        } else {
            header('Location: login.php?error=invalid'); // Incorrect password
            exit();
        }
    } else {
        header('Location: login.php?error=invalid'); // Email not found
        exit();
    }
}
?>
