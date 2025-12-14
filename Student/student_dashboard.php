<?php
session_start();
include '../db_connect.php'; // Ensure this connects to `olims_db`

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student details
$stmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.03);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Student Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard Content -->
<div class="dashboard-container text-center">
    <h2 class="mt-4">Welcome, <span class="text-primary"><?= htmlspecialchars($full_name); ?></span></h2>
    <p class="lead">Manage your lab bookings and experiments efficiently.</p>

    <!-- Feature Cards -->
    <div class="row g-4 mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <i class="fas fa-flask fa-3x text-danger"></i>
                    <h5 class="card-title mt-3">View Available Labs</h5>
                    <p class="card-text">See the list of available laboratories.</p>
                    <a href="view_labs.php" class="btn btn-danger">View Labs</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-success"></i>
                    <h5 class="card-title mt-3">Book a Lab</h5>
                    <p class="card-text">Reserve a laboratory for experiments.</p>
                    <a href="book_lab.php" class="btn btn-success">Book Now</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <i class="fas fa-history fa-3x text-warning"></i>
                    <h5 class="card-title mt-3">View Experiment </h5>
                    <p class="card-text">Track past experiments and results.</p>
                    <a href="view_experiments.php" class="btn btn-warning">View Experiment</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <i class="fas fa-upload fa-3x text-primary"></i>
                    <h5 class="card-title mt-3">Submit Experiment Results</h5>
                    <p class="card-text">Upload and submit your experiment data.</p>
                    <a href="submit_experiment.php" class="btn btn-primary">Submit</a>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-3x text-info"></i>
                    <h5 class="card-title mt-3">Track Booking Status</h5>
                    <p class="card-text">Check the approval status of your lab bookings.</p>
                    <a href="track_bookings.php" class="btn btn-info">Track Status</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
