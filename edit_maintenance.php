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

// Fetch maintenance record
$stmt = $conn->prepare("SELECT * FROM maintenance WHERE maintenance_id = ?");
$stmt->bind_param("i", $maintenance_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Record not found!";
    header("Location: manage_maintenance.php");
    exit();
}

$maintenance = $result->fetch_assoc();

// Fetch equipment list
$equipment_query = "SELECT equipment_id, equipment_name FROM equipment";
$equipment_result = $conn->query($equipment_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $equipment_id = $_POST['equipment_id'];
    $technician_name = trim($_POST['technician_name']);
    $maintenance_date = $_POST['maintenance_date'];
    $remarks = trim($_POST['remarks']);

    // Update record
    $stmt = $conn->prepare("UPDATE maintenance SET equipment_id=?, technician_name=?, maintenance_date=?, remarks=? WHERE maintenance_id=?");
    $stmt->bind_param("isssi", $equipment_id, $technician_name, $maintenance_date, $remarks, $maintenance_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Maintenance record updated successfully!";
        header("Location: manage_maintenance.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating maintenance record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Maintenance Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Maintenance Record</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Equipment</label>
                <select name="equipment_id" class="form-control" required>
                    <?php while ($row = $equipment_result->fetch_assoc()): ?>
                        <option value="<?= $row['equipment_id']; ?>" <?= ($row['equipment_id'] == $maintenance['equipment_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($row['equipment_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Technician Name</label>
                <input type="text" name="technician_name" class="form-control" value="<?= htmlspecialchars($maintenance['technician_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Maintenance Date</label>
                <input type="date" name="maintenance_date" class="form-control" value="<?= htmlspecialchars($maintenance['maintenance_date']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control"><?= htmlspecialchars($maintenance['remarks']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Update Record</button>
            <a href="manage_maintenance.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
