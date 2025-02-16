<?php
session_start();

// Check if parcel ID (pid) is passed in the URL
$pid = isset($_GET['pid']) ? $_GET['pid'] : null;

if (!$pid) {
    echo "Invalid parcel ID.";
    exit();
}

// Database connection
$DB_USERNAME = "root";  
$DB_PASS = "";         
$DB_HOSTNAME = "localhost:3306";
$DB_NAME = "project"; 

$conn = mysqli_connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASS, $DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the status of the parcel and the accepted person's email
$sql = "SELECT recipient_email, status FROM trusted_person WHERE parcel_id = ? AND status = 'accepted' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();

$accepted_email = null;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $accepted_email = $row['recipient_email'];
}

$stmt->close();
$conn->close();

if ($accepted_email) {
    header("Location: confirmation.php?pid=" . $pid); // Redirect to confirmation page if accepted
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loading Animation</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #1e1e2f;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
      color: white;
      overflow: hidden;
    }

    .loading-container {
      text-align: center;
      font-size: 2rem;
      letter-spacing: 2px;
    }

    .loading-text {
      display: inline-block;
    }

    .dots::after {
      content: '';
      display: inline-block;
      width: 0;
      animation: addDots 1.5s steps(3, end) infinite;
    }

    .spinner {
      width: 50px;
      height: 50px;
      border: 5px solid transparent;
      border-top: 5px solid #ff6f61;
      border-radius: 50%;
      animation: spin 1s linear infinite, changeColor 3s infinite;
      margin: 0 auto;
    }

    @keyframes changeColor {
      0% {
        border-top-color: #ff6f61;
      }
      28% {
        border-top-color: #4caf50;
      }
      58% {
        border-top-color: #2196f3;
      }
      85% {
        border-top-color: #ffeb3b;
      }
      110% {
        border-top-color: #ff6f61;
      }
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

    @keyframes addDots {
      0% {
        content: '';
      }
      33% {
        content: '.';
      }
      66% {
        content: '..';
      }
      100% {
        content: '...';
      }
    }
  </style>
</head>
<body>
  <div class="loading-container">
    <center>
      <div class="spinner"></div>
      <br>
      <span class="loading-text">Loading</span><span class="dots"></span>
    </center>
  </div>

  <script>
    // Reload the page every 2 seconds to check if someone has accepted the parcel
    setTimeout(function() {
      location.reload();  // Refresh the page after 2 seconds to check for updates
    }, 2000);
  </script>
</body>
</html>
