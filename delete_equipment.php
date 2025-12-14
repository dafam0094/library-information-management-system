<?php
session_start();
include 'db_connect.php';

// Restrict access to admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$sql = "DELETE FROM equipment WHERE equipment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

$stmt->execute();
$_SESSION['success'] = "Equipment deleted successfully!";
header("Location: manage_equipment.php");
exit();
?>
