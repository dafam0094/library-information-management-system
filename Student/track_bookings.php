<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT lb.booking_id, l.lab_name, lb.booking_date, lb.start_time, lb.end_time, lb.status 
        FROM lab_bookings lb
        JOIN laboratories l ON lb.lab_id = l.lab_id
        WHERE lb.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">My Lab Bookings</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>Lab Name</th>
                <th>Booking Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['lab_name']); ?></td>
                    <td><?= htmlspecialchars($row['booking_date']); ?></td>
                    <td><?= htmlspecialchars($row['start_time']); ?></td>
                    <td><?= htmlspecialchars($row['end_time']); ?></td>
                    <td>
                        <?php
                        $status = $row['status'];
                        $badge_class = ($status == 'approved') ? 'success' : (($status == 'rejected') ? 'danger' : 'warning');
                        ?>
                        <span class="badge bg-<?= $badge_class; ?>"><?= ucfirst($status); ?></span>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
