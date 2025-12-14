<?php
session_start();
include 'db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Initialize search and filter variables
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Fetch distinct statuses for filtering
$status_query = "SELECT DISTINCT status FROM equipment";
$status_result = $conn->query($status_query);

// Fetch filtered equipment data
$sql = "SELECT e.*, l.lab_name FROM equipment e JOIN laboratories l ON e.lab_id = l.lab_id WHERE 1=1";
if ($search !== '') {
    $sql .= " AND (e.equipment_name LIKE ? OR l.lab_name LIKE ? )";
}
if ($status_filter !== '') {
    $sql .= " AND e.status = ?";
}

$stmt = $conn->prepare($sql);
if ($search !== '' && $status_filter !== '') {
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $status_filter);
} elseif ($search !== '') {
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
} elseif ($status_filter !== '') {
    $stmt->bind_param("s", $status_filter);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Equipment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Manage Equipment</h2>
        
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
        
        <a href="add_equipment.php" class="btn btn-primary mb-3">Add New Equipment</a>
        
        <!-- Search and Filter Form -->
        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search equipment..." value="<?= htmlspecialchars($search); ?>">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <?php while ($status = $status_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($status['status']); ?>" <?= ($status_filter == $status['status']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($status['status']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-success">Search</button>
            <a href="manage_equipment.php" class="btn btn-secondary">Reset</a>
        </form>
        
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Lab Assigned</th>
                    <th>Status</th>
                    <th>Purchase Date</th>
                    <th>Last Maintenance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['equipment_id']; ?></td>
                        <td><?= htmlspecialchars($row['equipment_name']); ?></td>
                        <td><?= htmlspecialchars($row['lab_name']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td><?= htmlspecialchars($row['purchase_date']); ?></td>
                        <td><?= htmlspecialchars($row['last_maintenance']); ?></td>
                        <td>
                            <a href="edit_equipment.php?id=<?= $row['equipment_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_equipment.php?id=<?= $row['equipment_id']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this equipment?');">
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
