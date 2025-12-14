<?php
session_start();
include 'db_connect.php';

// Restrict access to admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_equipment.php");
    exit();
}

$id = $_GET['id'];

// Fetch equipment details
$stmt = $conn->prepare("SELECT * FROM equipment WHERE equipment_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$equipment = $result->fetch_assoc();

if (!$equipment) {
    $_SESSION['error'] = "Equipment not found!";
    header("Location: manage_equipment.php");
    exit();
}

// Fetch labs
$labs = $conn->query("SELECT lab_id, lab_name FROM laboratories");

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['equipment_name'];
    $lab_id = $_POST['lab_id'];
    $status = $_POST['status'];
    $purchase_date = $_POST['purchase_date'];
    $last_maintenance = $_POST['last_maintenance'];

    $update_sql = "UPDATE equipment SET equipment_name=?, lab_id=?, status=?, purchase_date=?, last_maintenance=? WHERE equipment_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sisssi", $name, $lab_id, $status, $purchase_date, $last_maintenance, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Equipment updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating equipment.";
    }
    header("Location: manage_equipment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Equipment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Edit Equipment</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Equipment Name</label>
            <input type="text" name="equipment_name" class="form-control" value="<?= htmlspecialchars($equipment['equipment_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Assign to Lab</label>
            <select name="lab_id" class="form-control" required>
                <?php while ($row = $labs->fetch_assoc()): ?>
                    <option value="<?= $row['lab_id']; ?>" <?= ($row['lab_id'] == $equipment['lab_id']) ? 'selected' : ''; ?>>
                        <?= $row['lab_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="available" <?= ($equipment['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                <option value="in_use" <?= ($equipment['status'] == 'in_use') ? 'selected' : ''; ?>>In Use</option>
                <option value="under_maintenance" <?= ($equipment['status'] == 'under_maintenance') ? 'selected' : ''; ?>>Under Maintenance</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Purchase Date</label>
            <input type="date" name="purchase_date" class="form-control" value="<?= $equipment['purchase_date']; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Last Maintenance</label>
            <input type="date" name="last_maintenance" class="form-control" value="<?= $equipment['last_maintenance']; ?>">
        </div>
        <button type="submit" class="btn btn-success">Update Equipment</button>
    </form>
</div>
</body>
</html>
