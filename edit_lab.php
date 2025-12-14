<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid lab ID!";
    header("Location: manage_labs.php");
    exit();
}

$lab_id = intval($_GET['id']);

// Fetch lab details
$sql = "SELECT lab_name, location, capacity, status FROM laboratories WHERE lab_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lab_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($lab_name, $location, $capacity, $status);
$stmt->fetch();

if ($stmt->num_rows == 0) {
    $_SESSION['error'] = "Lab not found!";
    header("Location: manage_labs.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab_name = trim($_POST['lab_name']);
    $location = trim($_POST['location']);
    $capacity = intval($_POST['capacity']);
    $status = $_POST['status'];

    $update_sql = "UPDATE laboratories SET lab_name = ?, location = ?, capacity = ?, status = ? WHERE lab_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssisi", $lab_name, $location, $capacity, $status, $lab_id);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Lab updated successfully!";
        header("Location: manage_labs.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating lab!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laboratory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Edit Laboratory</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Lab Name</label>
            <input type="text" name="lab_name" class="form-control" value="<?= htmlspecialchars($lab_name) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($location) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" value="<?= htmlspecialchars($capacity) ?>" required min="1">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="available" <?= $status == 'available' ? 'selected' : '' ?>>Available</option>
                <option value="maintenance" <?= $status == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Lab</button>
        <a href="manage_labs.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
