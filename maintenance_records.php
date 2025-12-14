<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Initialize search filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch maintenance records with filtering
$sql = "SELECT m.*, e.equipment_name 
        FROM maintenance m
        JOIN equipment e ON m.equipment_id = e.equipment_id
        WHERE 1=1";

if ($search !== '') {
    $sql .= " AND (e.equipment_name LIKE ? OR m.technician_name LIKE ?)";
}

$stmt = $conn->prepare($sql);

if ($search !== '') {
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Maintenance Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Manage Maintenance Records</h2>

        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <a href="add_maintenance.php" class="btn btn-primary mb-3">Add New Maintenance Record</a>

        <!-- Search Form -->
        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search by equipment or technician..." value="<?= htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-success">Search</button>
            <a href="maintenance_records.php" class="btn btn-secondary">Reset</a>
        </form>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Equipment</th>
                    <th>Technician</th>
                    <th>Maintenance Date</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['maintenance_id']; ?></td>
                        <td><?= htmlspecialchars($row['equipment_name']); ?></td>
                        <td><?= htmlspecialchars($row['technician_name']); ?></td>
                        <td><?= htmlspecialchars($row['maintenance_date']); ?></td>
                        <td><?= htmlspecialchars($row['remarks']); ?></td>
                        <td>
                            <a href="edit_maintenance.php?id=<?= $row['maintenance_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_maintenance.php?id=<?= $row['maintenance_id']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this record?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
