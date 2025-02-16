
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Order History</title>
    <link rel="stylesheet" href="accepter.css">
   


 <body>
    <header>
        <div class="logo">
            <img src="Preview_enhanced.png" alt="Safe Pick Logo" style="border-radius: 50%;">
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Overlay for background dimming -->
    <div class="overlay" id="overlay"></div>

    <div class="container">
        <div class="popup" id="popup">
            <img src="Preview_enhanced.png" alt="Truck" style="width: 50%; height: auto;">
            <h2>THANK YOU FOR PICKING THE ORDER</h2>
            <p>Have a great day!</p>
            
        </div>
    </div>

    <!-- <div>
    <?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';  // Include the PHPMailer autoloader

    // Database connection
    $servername = "localhost:3306"; 
    $db_username = "root"; 
    $db_password = ""; 
    $dbname = "project"; 

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check for token in URL
    if (!isset($_GET['token'])) {
        echo "Invalid request. Token is missing.";
        exit();
    }

    $token = $_GET['token'];

    // Start a transaction to lock rows and manage first-come, first-served handling
    $conn->begin_transaction();

    try {
        // Check parcel status with a row lock (FOR UPDATE) to avoid multiple concurrent updates
        $check_sql = "SELECT p.status, tp.parcel_id, tp.recipient_email
                    FROM parcels p
                    JOIN trusted_person tp ON tp.parcel_id = p.pid
                    WHERE tp.token = ? FOR UPDATE";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $token);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            echo "Row found for the given token.<br>";
            $parcel_status = $row['status'];
            $parcel_id = $row['parcel_id'];
            $recipient_email = $row['recipient_email'];
            echo "Debug - Retrieved Parcel Status: " . $parcel_status . "<br>";

            if ($parcel_status == 'Accepted') {
                // If already accepted, inform the user and rollback transaction
                echo "Token received: " . $token . "<br>";
                echo "Parcel Status: " . $parcel_status . "<br>";
                echo "This parcel has already been accepted by someone else.";
                $conn->rollback();
                exit();
            }

            // Update parcel status to 'Accepted' and record who accepted it
            $update_sql = "UPDATE parcels SET status = 'Accepted', recipient_email = ? WHERE pid = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $recipient_email, $parcel_id);
            $update_stmt->execute();

            // Mark this token as 'Accepted' in the trusted_person table
            $trusted_person_update_sql = "UPDATE trusted_person SET status = 'Accepted' WHERE token = ?";
            $trusted_person_update_stmt = $conn->prepare($trusted_person_update_sql);
            $trusted_person_update_stmt->bind_param("s", $token);
            $trusted_person_update_stmt->execute();

            // Commit the transaction to finalize changes
            $conn->commit();

            echo "<h3>Thank you! The parcel has been accepted by:</h3>";
            echo "<p><strong>Acceptor's Email:</strong> " . $recipient_email . "</p>";
        } else {    
            echo "Invalid token or no rows found for the given token.<br>";
            $conn->rollback();
        }
        } catch (Exception $e) {
            // Rollback in case of an error
            $conn->rollback();
            echo "An error occurred. Please try again later.";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div> -->

    <script src="popup.js"></script>

    <footer>
        <p>© 2024. All Rights Reserved.</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
    </footer>
</body>
</html>