<?php
session_start();
include 'db_connect.php';

// Restrict access to admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Fetch labs for selection
$labs = $conn->query("SELECT lab_id, lab_name FROM laboratories");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['equipment_name'];
    $lab_id = $_POST['lab_id'];
    $status = $_POST['status'];
    $purchase_date = $_POST['purchase_date'];
    $last_maintenance = $_POST['last_maintenance'];

    $sql = "INSERT INTO equipment (equipment_name, lab_id, status, purchase_date, last_maintenance) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $name, $lab_id, $status, $purchase_date, $last_maintenance);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Equipment added successfully!";
    } else {
        $_SESSION['error'] = "Error adding equipment.";
    }
    header("Location: manage_equipment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Equipment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Add New Equipment</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Equipment Name</label>
            <input type="text" name="equipment_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Assign to Lab</label>
            <select name="lab_id" class="form-control" required>
                <option value="">Select Lab</option>
                <?php while ($row = $labs->fetch_assoc()): ?>
                    <option value="<?= $row['lab_id']; ?>"><?= $row['lab_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="available">Available</option>
                <option value="in_use">In Use</option>
                <option value="under_maintenance">Under Maintenance</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Purchase Date</label>
            <input type="date" name="purchase_date" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Last Maintenance</label>
            <input type="date" name="last_maintenance" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Equipment</button>
    </form>
</div>
</body>
</html>
