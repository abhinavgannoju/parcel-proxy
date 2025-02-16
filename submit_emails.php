<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure the PHPMailer autoload file is included

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if the user is not logged in
    exit();
}

// Get the logged-in user's data from the session
$user_email = $_SESSION['email'];
$username = $_SESSION['username'];

// Get form data from the POST request
$pid = isset($_POST['pid']) ? trim($_POST['pid']) : null;
$pd = isset($_POST['pdescription']) ? trim($_POST['pdescription']) : '';
$email1 = trim($_POST['email1']);
$email2 = trim($_POST['email2']);
$email3 = trim($_POST['email3']);

$emails = [$email1, $email2, $email3];

// Validate email addresses
foreach ($emails as $email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format: $email<br>";
        exit();
    }
}

// Database connection
$DB_USERNAME = "root";
$DB_PASS = "";
$DB_HOSTNAME = "localhost:3306";
$DB_NAME = "project";

$conn = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID of the sender
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$uid = $result->fetch_assoc()['id'];

// Check if the Parcel ID exists in the parcels table
$query = "SELECT * FROM parcels WHERE pid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pid);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    // Insert parcel if it doesn't exist
    $stmt_insert = $conn->prepare("INSERT INTO parcels (pid, sender_email, parcel_description, sender_id) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("issi", $pid, $user_email, $pd, $uid);
    $stmt_insert->execute();
    $stmt_insert->close();
}

// Insert trusted_person data and send emails
foreach ($emails as $email) {
    if (!empty($email)) {
        $token = bin2hex(random_bytes(32)); // Generate unique token

        $stmt = $conn->prepare("INSERT INTO trusted_person (parcel_id, recipient_email, token) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $pid, $email, $token);

        if ($stmt->execute()) {
            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'pinkustar15@gmail.com';  // Your Gmail address
                $mail->Password = 'zzvp jrlc wyzu nxud';    // Your App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Email settings
                $mail->setFrom('pinkustar15@gmail.com', 'Package Pickup');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Book Order - Confirmation';
                $mail->Body = "
                    <p>Hi, this email was sent by <strong>$username</strong>.</p>
                    <p><strong>Parcel ID:</strong> $pid</p>
                    <p><strong>Parcel Description:</strong> $pd</p>
                    <p>Please accept or decline the book order by clicking one of the following buttons:</p>
                    <a href='http://172.27.37.191/dbmsproject/accepter.php?token=$token' style='
                        display: inline-block;
                        padding: 10px 20px;
                        font-size: 16px;
                        color: #ffffff;
                        background-color: #4CAF50;
                        text-decoration: none;
                        border-radius: 5px;
                        margin-right: 10px;'>Accept</a>
                    <a href='http://172.27.37.191/dbmsproject/decliner.php?token=$token' style='
                        display: inline-block;
                        padding: 10px 20px;
                        font-size: 16px;
                        color: #ffffff;
                        background-color: #f44336;
                        text-decoration: none;
                        border-radius: 5px;'>Decline</a>";

                $mail->AltBody = "Hi, this email was sent by $username.\nParcel ID: $pid\nParcel Description: $pd\nAccept: http://172.27.37.191/dbmsproject/accepter.php?token=$token\nDecline: http://172.27.37.191/dbmsproject/decliner.php?token=$token";

                $mail->send();
                echo "Email sent successfully to $email.<br>";
            } catch (Exception $e) {
                echo "Error sending email to $email: {$mail->ErrorInfo}<br>";
            }
        } else {
            echo "Error inserting data: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
}

// Close the database connection
$conn->close();

// Redirect to confirmation page
header("Location: confirmation.php?pid=$pid");
exit();
?>
