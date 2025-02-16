<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Order History</title>
    <link rel="stylesheet" href="accepter.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="Preview_enhanced.png" alt="Safe Pick Logo">
            
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
    <?php
        // Database connection
        $DB_USERNAME = "root";
        $DB_PASS = "";
        $DB_HOSTNAME = "localhost:3306";
        $DB_NAME = "project";
        $conn = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASS, $DB_NAME);

        // Get trusted_person ID from URL
        $trusted_person_id = $_GET['id'];

        // Check if this trusted person has already responded
        $stmt = $conn->prepare("SELECT parcel_id, recipient_email, status FROM trusted_person WHERE id = ?");
        $stmt->bind_param("i", $trusted_person_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && $row['status'] === 'Pending') {
            $parcel_id = $row['parcel_id'];
            $recipient_email = $row['recipient_email'];

            // Update status to 'Declined' and set response time in trusted_person
            $update = $conn->prepare("UPDATE trusted_person SET status = 'Declined', response_time = NOW() WHERE id = ?");
            $update->bind_param("i", $trusted_person_id);
            $update->execute();

            // Check if this is the first response for the parcel
            $first_response_check = $conn->prepare("
                SELECT recipient_email FROM parcels WHERE pid = ? AND recipient_email IS NULL
            ");
            $first_response_check->bind_param("i", $parcel_id);
            $first_response_check->execute();
            $first_response_check->store_result();

            if ($first_response_check->num_rows > 0) {
                // Update the parcel with the first response information
                $update_parcel = $conn->prepare("
                    UPDATE parcels
                    SET recipient_email = ?, first_response_status = 'Declined', first_response_time = NOW()
                    WHERE pid = ?
                ");
                $update_parcel->bind_param("si", $recipient_email, $parcel_id);
                $update_parcel->execute();
            }

            echo "You have declined the order.";
        } else {
            echo "This request has already been responded to.";
        }

        $stmt->close();
        $conn->close();
        ?>   
    </main>

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
