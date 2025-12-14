<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Check if booking_id is provided
if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    $_SESSION['error'] = "Invalid booking ID!";
    header("Location: manage_bookings.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);

// Delete booking
$sql = "DELETE FROM lab_bookings WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Booking deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete booking.";
}

$stmt->close();
$conn->close();

header("Location: manage_bookings.php");
exit();
?>
