<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}

// Fetch available labs
$sql = "SELECT * FROM laboratories WHERE status = 'available'";
$result = $conn->query($sql);

// Handle booking submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $lab_id = $_POST['lab_id'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $conn->prepare("INSERT INTO lab_bookings (user_id, lab_id, booking_date, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisss", $user_id, $lab_id, $booking_date, $start_time, $end_time);

    if ($stmt->execute()) {
        echo "<script>alert('Lab booked successfully! Waiting for approval.'); window.location='lecturer_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error booking lab. Try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Laboratory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Book a Laboratory</h2>
    <a href="view_lab.php" class="btn btn-primary mb-3">view Available Lab</a>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="lab_id" class="form-label">Select Laboratory</label>
            <select class="form-select" name="lab_id" required>
                <option value="">-- Select Lab --</option>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?= $row['lab_id']; ?>"><?= htmlspecialchars($row['lab_name']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="booking_date" class="form-label">Booking Date</label>
            <input type="date" class="form-control" name="booking_date" required>
        </div>
        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" class="form-control" name="start_time" required>
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" class="form-control" name="end_time" required>
        </div>
        <button type="submit" class="btn btn-success">Book Lab</button>
        <a href="lecturer_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
