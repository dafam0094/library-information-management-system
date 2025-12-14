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
$status_query = "SELECT DISTINCT status FROM lab_bookings";
$status_result = $conn->query($status_query);

// Fetch filtered lab bookings
$sql = "SELECT lb.*, u.full_name AS user_name, u.email, l.lab_name, l.location 
        FROM lab_bookings lb
        JOIN users u ON lb.user_id = u.user_id
        JOIN laboratories l ON lb.lab_id = l.lab_id
        WHERE 1=1";

if ($search !== '') {
    $sql .= " AND (u.full_name LIKE ? OR l.lab_name LIKE ?)";
}
if ($status_filter !== '') {
    $sql .= " AND lb.status = ?";
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
    <title>Manage Lab Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Manage Lab Bookings</h2>

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

        <a href="add_bookings.php" class="btn btn-primary mb-3">Add New Booking</a>

        <!-- Search and Filter Form -->
        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search by user or lab..." value="<?= htmlspecialchars($search); ?>">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <?php while ($status = $status_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($status['status']); ?>" <?= ($status_filter == $status['status']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($status['status']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-success">Search</button>
            <a href="manage_bookings.php" class="btn btn-secondary">Reset</a>
        </form>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Lab</th>
                    <th>Location</th>
                    <th>Booking Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['booking_id']; ?></td>
                        <td><?= htmlspecialchars($row['user_name']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['lab_name']); ?></td>
                        <td><?= htmlspecialchars($row['location']); ?></td>
                        <td><?= htmlspecialchars($row['booking_date']); ?></td>
                        <td><?= htmlspecialchars($row['start_time']); ?></td>
                        <td><?= htmlspecialchars($row['end_time']); ?></td>
                        <td><?= ucfirst(htmlspecialchars($row['status'])); ?></td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <a href="update_lab_booking.php?booking_id=<?= $row['booking_id']; ?>&status=approved" class="btn btn-success btn-sm">Approve</a>
                                <a href="update_lab_booking.php?booking_id=<?= $row['booking_id']; ?>&status=rejected" class="btn btn-danger btn-sm">Reject</a>
                            <?php endif; ?>
                            <a href="delete_lab_booking.php?booking_id=<?= $row['booking_id']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this booking?');">
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
