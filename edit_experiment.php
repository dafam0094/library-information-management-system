<?php
session_start();
include 'db_connect.php';

// Restrict access to admin and lab users
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'lab_user'])) {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Check if the experiment ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid experiment ID.";
    header("Location: manage_experiments.php");
    exit();
}

$experiment_id = $_GET['id'];

// Fetch experiment details
$stmt = $conn->prepare("SELECT * FROM experiments WHERE experiment_id = ?");
$stmt->bind_param("i", $experiment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Experiment not found.";
    header("Location: manage_experiments.php");
    exit();
}

$experiment = $result->fetch_assoc();
$stmt->close();

// Fetch available labs
$lab_query = "SELECT lab_id, lab_name FROM laboratories";
$lab_result = $conn->query($lab_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['experiment_title']);
    $description = trim($_POST['description']);
    $date = $_POST['experiment_date'];
    $lab_id = $_POST['lab_id'];

    // Validate input
    if (empty($title) || empty($description) || empty($date) || empty($lab_id)) {
        $_SESSION['error'] = "All fields are required.";
    } else {
        // Update experiment in the database
        $stmt = $conn->prepare("UPDATE experiments SET experiment_title = ?, description = ?, experiment_date = ?, lab_id = ? WHERE experiment_id = ?");
        $stmt->bind_param("sssii", $title, $description, $date, $lab_id, $experiment_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Experiment updated successfully!";
            header("Location: manage_experiments.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating experiment.";
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
    <title>Edit Experiment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Experiment</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="POST" class="p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label class="form-label">Lab</label>
                <select name="lab_id" class="form-select" required>
                    <?php while ($row = $lab_result->fetch_assoc()): ?>
                        <option value="<?= $row['lab_id']; ?>" <?= ($experiment['lab_id'] == $row['lab_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($row['lab_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="experiment_title" class="form-control" value="<?= htmlspecialchars($experiment['experiment_title']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($experiment['description']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="experiment_date" class="form-control" value="<?= $experiment['experiment_date']; ?>" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Update Experiment</button>
        </form>
    </div>
</body>
</html>
