<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Get maintenance record ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: manage_maintenance.php");
    exit();
}

$maintenance_id = intval($_GET['id']);

// Delete record
$stmt = $conn->prepare("DELETE FROM maintenance WHERE maintenance_id = ?");
$stmt->bind_param("i", $maintenance_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Maintenance record deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting maintenance record.";
}

header("Location: manage_maintenance.php");
exit();
