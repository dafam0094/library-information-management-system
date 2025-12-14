<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}

// Handle booking approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE lab_bookings SET status = ? WHERE booking_id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    $stmt->execute();
}

// Fetch pending bookings with lab names
$bookings = $conn->query("
    SELECT lb.booking_id, lb.booking_date, lb.status, l.lab_name 
    FROM lab_bookings lb
    JOIN laboratories l ON lb.lab_id = l.lab_id
    WHERE lb.status = 'Pending'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Lab Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2 class="mb-4">Approve Lab Bookings</h2>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Lab Name</th>
            <th>Booking Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $bookings->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['lab_name']) ?></td>
            <td><?= htmlspecialchars($row['booking_date']) ?></td>
            <td>
                <form method="post" class="d-inline">
                    <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                    <button type="submit" name="status" value="Approved" class="btn btn-success">Approve</button>
                    <button type="submit" name="status" value="Rejected" class="btn btn-danger">Reject</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<a href="lecturer_dashboard.php" class="btn btn-secondary">Back</a>
</body>
</html>
