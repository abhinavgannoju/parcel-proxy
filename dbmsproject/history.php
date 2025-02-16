<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: login.php'); // Redirect to login if session not set
    exit();
}

$email = $_SESSION['email']; // Get the username
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="history.css">
    <title>User History</title>
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
    <div class="center">
    <div class="center">
        <center>
    <?php
        include('conn.php');
        $sql="select *
        from parcels where
        sender_email='$email'and status='Accepted'";
        $res=mysqli_query($conn,$sql);
        // $parcelid='';
        // $recipemail='';
        // $date='';
        // $pdesc='';
        // if($result=mysqli_fetch_assoc($res))
        // {
        //     $parcelid=$result['pid'];
        //     $recipemail=$result['recipient_email'];
        //     $date=$result['created_at'];
        //     $pdesc=$result['parcel_description'];
        // }
        echo '<table border="1"><tr><th>parcel id</th><th>recipent email</th><th>booked on</th><th>description</th></tr>';
        while($result=mysqli_fetch_assoc($res))
        {
        echo '<tr><td>'. $result['pid'] . '</td><td>'.
        $result['recipient_email'] . '</td><td>' . $result['created_at'] .'</td><td>'.$result['parcel_description'].'</td></tr>';
        }
        echo '</table>';
    ?>
    </center>
    </div>

    </div>
    </main>
    
    <footer>
       
    </footer>

</body>
</html>
