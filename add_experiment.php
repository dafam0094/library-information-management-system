<?php
session_start();
include 'db_connect.php';

// Restrict access to admin and lab users
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'lab_user'])) {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Fetch available labs
$lab_query = "SELECT lab_id, lab_name FROM laboratories";
$lab_result = $conn->query($lab_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Logged-in user
    $lab_id = $_POST['lab_id'];
    $title = trim($_POST['experiment_title']);
    $description = trim($_POST['description']);
    $date = $_POST['experiment_date'];

    // Validate input
    if (empty($lab_id) || empty($title) || empty($description) || empty($date)) {
        $_SESSION['error'] = "All fields are required.";
    } else {
        // Insert experiment into the database
        $stmt = $conn->prepare("INSERT INTO experiments (user_id, lab_id, experiment_title, description, experiment_date) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $user_id, $lab_id, $title, $description, $date);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Experiment added successfully!";
            header("Location: manage_experiments.php");
            exit();
        } else {
            $_SESSION['error'] = "Error adding experiment.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Experiment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add Experiment</h2>

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

        <form method="POST" class="p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label class="form-label">Lab</label>
                <select name="lab_id" class="form-select" required>
                    <option value="">Select a Lab</option>
                    <?php while ($row = $lab_result->fetch_assoc()): ?>
                        <option value="<?= $row['lab_id']; ?>"><?= htmlspecialchars($row['lab_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="experiment_title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="experiment_date" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Experiment</button>
        </form>
    </div>
</body>
</html>
