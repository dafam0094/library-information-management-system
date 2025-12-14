<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Welcome, Admin</h2>
        <div class="row mt-4">

            <!-- Manage Users -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text">Add, edit, or remove users from the system.</p>
                        <a href="manage_users.php" class="btn btn-primary">Go to Users</a>
                    </div>
                </div>
            </div>

            <!-- Manage Labs -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Laboratories</h5>
                        <p class="card-text">View and update lab details and availability.</p>
                        <a href="manage_labs.php" class="btn btn-primary">Go to Labs</a>
                    </div>
                </div>
            </div>

            <!-- Manage Equipment -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Equipment</h5>
                        <p class="card-text">Add, maintain, or remove lab equipment.</p>
                        <a href="manage_equipment.php" class="btn btn-primary">Go to Equipment</a>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-4">
            <!-- Manage Bookings -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Bookings </h5>
                        <p class="card-text">Add, Approved, Reject or Delete booking </p>
                        <a href="manage_bookings.php" class="btn btn-primary">Go to Bookings</a>
                    </div>
                </div>
            </div>

            <!-- Maintenance Records -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Maintenance Records</h5>
                        <p class="card-text">View and update maintenance records.</p>
                        <a href="maintenance_records.php" class="btn btn-primary">Go to Maintenance</a>
                    </div>
                </div>
            </div>

            <!-- experiments  -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Experiments </h5>
                        <p class="card-text">View and update experiments </p>
                        <a href="manage_experiments.php" class="btn btn-primary">Go to experiments </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
