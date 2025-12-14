<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Check if booking_id and status are provided
if (!isset($_GET['booking_id'], $_GET['status']) || !in_array($_GET['status'], ['approved', 'rejected'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: manage_bookings.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);
$status = $_GET['status'];

// Update booking status
$sql = "UPDATE lab_bookings SET status = ? WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $booking_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Booking successfully " . ucfirst($status) . "!";
} else {
    $_SESSION['error'] = "Failed to update booking status.";
}

$stmt->close();
$conn->close();

header("Location: manage_bookings.php");
exit();
?>
