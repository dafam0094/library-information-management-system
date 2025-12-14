<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}

// Fetch students and labs
$students = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'student'");
$labs = $conn->query("SELECT lab_id, lab_name FROM laboratories");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $lab_id = $_POST['lab_id'];
    $title = $_POST['experiment_title'];
    $description = $_POST['description'];
    $date = $_POST['experiment_date'];

    $stmt = $conn->prepare("INSERT INTO experiments (user_id, lab_id, experiment_title, description, experiment_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $student_id, $lab_id, $title, $description, $date);
    
    if ($stmt->execute()) {
        $success = "Experiment assigned successfully!";
    } else {
        $error = "Failed to assign experiment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Experiment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Assign Experiment</h2>
<?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="post" class="card p-4">
    <div class="mb-3">
        <label>Student</label>
        <select name="student_id" class="form-control" required>
            <option value="">Select Student</option>
            <?php while ($row = $students->fetch_assoc()) { ?>
                <option value="<?= $row['user_id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
            <?php } ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label>Lab</label>
        <select name="lab_id" class="form-control" required>
            <option value="">Select Lab</option>
            <?php while ($row = $labs->fetch_assoc()) { ?>
                <option value="<?= $row['lab_id'] ?>"><?= htmlspecialchars($row['lab_name']) ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Experiment Title</label>
        <input type="text" name="experiment_title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" required></textarea>
    </div>

    <div class="mb-3">
        <label>Experiment Date</label>
        <input type="date" name="experiment_date" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Assign Experiment</button>
    <a href="lecturer_dashboard.php" class="btn btn-secondary">Back</a>
</form>

</body>
</html>
