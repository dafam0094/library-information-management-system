<?php
session_start();
include 'db_connect.php';

// Ensure admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Fetch all equipment for selection
$equipment_query = "SELECT equipment_id, equipment_name FROM equipment";
$equipment_result = $conn->query($equipment_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $equipment_id = $_POST['equipment_id'];
    $technician_name = $_POST['technician_name'];
    $maintenance_date = $_POST['maintenance_date'];
    $remarks = $_POST['remarks'];

    $insert_query = "INSERT INTO maintenance (equipment_id, technician_name, maintenance_date, remarks)
                     VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("isss", $equipment_id, $technician_name, $maintenance_date, $remarks);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Maintenance record added successfully!";
        header("Location: manage_maintenance.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add maintenance record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Maintenance Record</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Equipment</label>
                <select name="equipment_id" class="form-select" required>
                    <option value="" disabled selected>Select Equipment</option>
                    <?php while ($equip = $equipment_result->fetch_assoc()): ?>
                        <option value="<?= $equip['equipment_id']; ?>">
                            <?= htmlspecialchars($equip['equipment_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Technician Name</label>
                <input type="text" name="technician_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Maintenance Date</label>
                <input type="date" name="maintenance_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Add Maintenance</button>
            <a href="manage_maintenance.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
