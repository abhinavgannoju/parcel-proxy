<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$servername = "localhost:3306"; 
$db_username = "root"; 
$db_password = ""; 
$dbname = "project"; 

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data and validate inputs
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password
$verification_code = bin2hex(random_bytes(16)); // Generate a random verification code

// Check if email already exists
$check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$check_email->store_result();

if ($check_email->num_rows > 0) {
    echo "Email already registered. Please use another email.";
} else {
    // Insert data into the database
    $sql = "INSERT INTO users (username, email, password, verification_code) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $verification_code);
   
    if ($stmt->execute()) {
        // Send verification email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP(); 
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true; 
            $mail->Username = 'pinkustar15@gmail.com'; 
            $mail->Password = 'zzvp jrlc wyzu nxud'; // Your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('pinkustar15@gmail.com', 'Package Pickup');
            $mail->addAddress($email); 

            // Content
            $mail->isHTML(true); 
            $mail->Subject = 'Email Verification';
            $mail->Body = "Click the link to verify your email: <a href='http://localhost/dbmsproject/verify.php?code=$verification_code'>Verify Email</a>";
            $mail->AltBody = "Click the link to verify your email: http://localhost/dbmsproject/verify.php?code=$verification_code";

            $mail->send();
            echo "Registration successful! Please check your email to verify your account.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Close statements and connection
$check_email->close();
// $stmt->close();
$conn->close();
?>
