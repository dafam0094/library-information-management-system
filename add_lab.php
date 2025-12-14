<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab_name = trim($_POST['lab_name']);
    $location = trim($_POST['location']);
    $capacity = intval($_POST['capacity']);
    $status = $_POST['status'];

    // Prevent duplicate lab names
    $check_sql = "SELECT lab_id FROM laboratories WHERE lab_name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $lab_name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['error'] = "Lab name already exists!";
    } else {
        // Insert new lab
        $sql = "INSERT INTO laboratories (lab_name, location, capacity, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $lab_name, $location, $capacity, $status);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Lab added successfully!";
            header("Location: manage_labs.php");
            exit();
        } else {
            $_SESSION['error'] = "Error adding lab!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Laboratory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Add New Laboratory</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Lab Name</label>
            <input type="text" name="lab_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" required min="1">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="available">Available</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Add Lab</button>
        <a href="manage_labs.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
