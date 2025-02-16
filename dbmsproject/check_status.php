<?php
// Include database connection
include('conn.php');

// Get parcel ID from the GET request
$pid = isset($_GET['pid']) ? $_GET['pid'] : '';

// Prepare the SQL query to check the status of the parcel
$sql = "SELECT recipient_email, status FROM trusted_person WHERE parcel_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $pid);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the status of the parcel
$status = 'pending';  // Default status
$acceptedBy = null;

if ($result && $row = $result->fetch_assoc()) {
    $status = $row['status'];
    $acceptedBy = $row['recipient_email'];
}

// Return the status as a JSON response
echo json_encode([
    'status' => $status,
    'email' => $acceptedBy
]);

$stmt->close();
$conn->close();
?>
