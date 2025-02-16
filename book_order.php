<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: login.php'); // Redirect to login if session not set
    exit();
}

$email = $_SESSION['email']; // Get logged-in user's email

// Database connection
include('conn.php');

// Fetch trusted emails for the logged-in user
$trusted_emails = [];

$sql = "
SELECT DISTINCT trusted_person.recipient_email 
FROM trusted_person
JOIN parcels ON parcels.pid = trusted_person.parcel_id
JOIN users ON users.id = parcels.sender_id
WHERE users.email = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $trusted_emails[] = $row['recipient_email'];
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Order Form</title>
    <link rel="stylesheet" href="book_order.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="Preview_enhanced.png" alt="Safe Pick Logo">
        </div>
        <nav>
            <ul>
                <li><a href="main_page.php">Home</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="right-section">
            <div class="form-box">
                <h2>Submit Emails</h2>
                <form action="submit_emails.php" method="POST">
                    <div class="input-box">
                        <label for="pid">Parcel ID</label>
                        <input type="text" id="pid" name="pid" required>
                    </div>
                    <div class="input-box">
                        <label for="pdescription">Parcel Description</label>
                        <input type="text" id="pdescription" name="pdescription" required>
                    </div>

                    <div class="input-box">
                        <label for="email1">Email 1</label>
                        <input type="text" id="email1" name="email1" list="emailOptions" required onchange="filterEmails(1)">
                    </div>

                    <div class="input-box">
                        <label for="email2">Email 2</label>
                        <input type="text" id="email2" name="email2" list="emailOptions" required onchange="filterEmails(2)">
                    </div>

                    <div class="input-box">
                        <label for="email3">Email 3</label>
                        <input type="text" id="email3" name="email3" list="emailOptions" required>
                    </div>

                    <datalist id="emailOptions">
                        <?php foreach ($trusted_emails as $email): ?>
                            <option value="<?= htmlspecialchars($email); ?>"></option>
                        <?php endforeach; ?>
                    </datalist>

                    <button type="submit" class="btn">Send</button>
                </form>
                
            </div>
        </div>
    </main>

    <script>
        function filterEmails(selectedIndex) {
            const email1 = document.getElementById('email1').value.toLowerCase();
            const email2 = document.getElementById('email2').value.toLowerCase();
            const email3 = document.getElementById('email3').value.toLowerCase();

            const allEmails = [email1, email2, email3];

            [1, 2, 3].forEach(index => {
                const inputElement = document.getElementById('email' + index);
                const options = document.getElementById('emailOptions').options;

                for (let option of options) {
                    const optionValue = option.value.toLowerCase();
                    if (allEmails.includes(optionValue) && optionValue !== allEmails[index - 1]) {
                        option.style.display = 'none'; // Hide selected emails
                    } else {
                        option.style.display = 'block'; // Show if not selected
                    }
                }
            });
        }

        // Parcel Status Checking (AJAX)
        function checkParcelStatus(pid) {
            fetch(`check_status.php?pid=${pid}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'accepted') {
                        document.getElementById('acceptorDetails').style.display = 'block';
                        document.getElementById('acceptorEmail').textContent = `Parcel accepted by: ${data.email}`;
                        document.getElementById('statusText').textContent = "Parcel has been accepted!";
                        clearInterval(statusInterval); // Stop checking once accepted
                    } else if (data.status === 'pending') {
                        document.getElementById('statusText').textContent = "Waiting for acceptance...";
                    }
                })
                .catch(error => console.error("Error checking status:", error));
        }

        var pid = "<?php echo isset($_GET['pid']) ? $_GET['pid'] : ''; ?>";
        if (pid) {
            var statusInterval = setInterval(function() {
                checkParcelStatus(pid);
            }, 5000); // Check every 5 seconds
        }
    </script>
</body>
</html>
