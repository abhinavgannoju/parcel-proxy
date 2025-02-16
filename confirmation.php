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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background: #1e1e2f;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
            font-family: Arial, sans-serif;
        }

        .message {
            font-size: 3rem;
            font-weight: bold;
            position: absolute;
            text-shadow: 0 0 10px #fff;
            opacity: 0;
            animation: fadeIn 2s ease-out forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: radial-gradient(circle, #57a6e6, transparent);
            border-radius: 50%;
            animation: explode 1s ease-out forwards;
        }

        .particle1 {
            position: absolute;
            width: 10px;
            height: 10px;
            background: radial-gradient(circle, #ff0000, transparent);
            border-radius: 50%;
            animation: explode 1s ease-out forwards;
        }

        @keyframes explode {
            0% {
                opacity: 1;
                transform: translate(0, 0) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(calc(var(--x) * 1px), calc(var(--y) * 1px)) scale(0.5);
            }
        }

        /* Loading Animation Style */
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

    <!-- Success Message if Accepted Email Found -->
    <div class="message">
        <center>Congratulations!<br>Your order has been picked by <br>   <span id="acceptorEmail"></span></center>
    </div>

    <!-- Loading Animation if No Accepted Email -->
    <div id="loadingContainer" class="loading-container" style="display:none;">
        <center>
            <div class="spinner"></div>
            <br>
            <span class="loading-text">Loading</span><span class="dots"></span>
        </center>
    </div>

    <script>
        // Handle Particles for Animation
        <?php if ($accepted_email): ?>
            document.getElementById('acceptorEmail').textContent = "<?php echo $accepted_email; ?>";
        <?php else: ?>
            // Show loading animation if no one has accepted
            document.getElementById('loadingContainer').style.display = 'block';
            document.getElementsByClassName('message')[0].style.display = 'none';
        <?php endif; ?>
        const particleCount = 100;
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            if(i % 2 === 0) {
                particle.className = 'particle';
            } else {
                particle.className = 'particle1';
            }
            const angle = Math.random() * 2 * Math.PI;
            const distance = Math.random() * 300;
            particle.style.setProperty('--x', Math.cos(angle) * distance);
            particle.style.setProperty('--y', Math.sin(angle) * distance);
            particle.style.left = `${window.innerWidth / 2}px`;
            particle.style.top = `${window.innerHeight / 2}px`;
            document.body.appendChild(particle);
            particle.addEventListener('animationend', () => particle.remove());
        }

        // Display the Accepting Email or Loading Animation
    </script>
</body>
</html>
