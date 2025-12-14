<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_experiments.php");
    exit();
}

$experiment_id = $_GET['id'];

// Delete the experiment
$stmt = $conn->prepare("DELETE FROM experiments WHERE experiment_id = ?");
$stmt->bind_param("i", $experiment_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Experiment deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting experiment.";
}

$stmt->close();
header("Location: manage_experiments.php");
exit();
?>
