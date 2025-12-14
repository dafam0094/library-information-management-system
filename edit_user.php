<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch user details
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT full_name, email, role FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($full_name, $email, $role);
    $stmt->fetch();
    $stmt->close();
}

// Update user details (including password if provided)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        // Hash the new password using SHA-256
        $hashed_password = hash('sha256', $password);
        $sql = "UPDATE users SET full_name = ?, email = ?, role = ?, password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $full_name, $email, $role, $hashed_password, $user_id);
    } else {
        // Update without changing password
        $sql = "UPDATE users SET full_name = ?, email = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $full_name, $email, $role, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update user!";
    }

    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Edit User</h2>
    <form action="edit_user.php" method="POST">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($full_name) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-control">
                <option value="student" <?= $role === 'student' ? 'selected' : '' ?>>Student</option>
                <option value="lecturer" <?= $role === 'lecturer' ? 'selected' : '' ?>>Lecturer</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password (Optional)</label>
            <input type="password" name="password" class="form-control">
            <small class="text-muted">Leave blank if you do not want to change the password.</small>
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
