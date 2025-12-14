<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: manage_labs.php");
    exit();
}

// Check if lab ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: manage_labs.php");
    exit();
}

$lab_id = intval($_GET['id']);

// Delete lab from database
$sql = "DELETE FROM laboratories WHERE lab_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lab_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Lab deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting lab!";
}

header("Location: manage_labs.php");
exit();
?>
