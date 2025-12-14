<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Fetch equipment list
$equipment_query = "SELECT equipment_id, equipment_name FROM equipment";
$equipment_result = $conn->query($equipment_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $equipment_id = $_POST['equipment_id'];
    $technician_name = trim($_POST['technician_name']);
    $maintenance_date = $_POST['maintenance_date'];
    $remarks = trim($_POST['remarks']);

    // Insert data
    $stmt = $conn->prepare("INSERT INTO maintenance (equipment_id, technician_name, maintenance_date, remarks) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $equipment_id, $technician_name, $maintenance_date, $remarks);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Maintenance record added successfully!";
        header("Location: manage_maintenance.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding maintenance record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Maintenance Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add Maintenance Record</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Equipment</label>
                <select name="equipment_id" class="form-control" required>
                    <option value="">Select Equipment</option>
                    <?php while ($row = $equipment_result->fetch_assoc()): ?>
                        <option value="<?= $row['equipment_id']; ?>"><?= htmlspecialchars($row['equipment_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Technician Name</label>
                <input type="text" name="technician_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Maintenance Date</label>
                <input type="date" name="maintenance_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Record</button>
            <a href="maintenance_records.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
