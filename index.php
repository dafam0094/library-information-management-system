<?php
session_start();
include 'db_connect.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the input password using SHA2(256)
    $hashed_input_password = hash('sha256', $password);

    $sql = "SELECT user_id, full_name, password, role FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $hashed_input_password);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $full_name, $db_password, $role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;

        // Redirect based on role
        if ($role == 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($role == 'lecturer') {
            header("Location: lecturer/lecturer_dashboard.php");
        } else {
            header("Location: student/student_dashboard.php");
        }
        exit();
    } else {
        $error_message = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLASU | Laboratory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('image/plasu.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }
        .welcome-text {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            margin-bottom: 20px;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .form-control {
            border-radius: 20px;
        }
        .btn {
            border-radius: 20px;
            width: 100%;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .register-link {
            margin-top: 10px;
            display: block;
        }
    </style>
</head>
<body>

<p class="welcome-text">Welcome to PLASU Laboratory Information Management System</p>

<div class="login-container">
    <h3 class="mb-4">User Login</h3>

    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger error-message"><?= htmlspecialchars($error_message); ?></div>
    <?php } ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-success register-link">Register Here</a>
    </form>
</div>

</body>
</html>
