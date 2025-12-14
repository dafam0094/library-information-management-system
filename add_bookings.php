<?php
session_start();
include 'db_connect.php';

// Restrict access to logged-in users
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in to book a lab!";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // User making the booking

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab_id = isset($_POST['lab_id']) ? intval($_POST['lab_id']) : 0;
    $booking_date = isset($_POST['booking_date']) ? trim($_POST['booking_date']) : '';
    $start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
    $end_time = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';

    // Validate input fields
    if ($lab_id <= 0 || empty($booking_date) || empty($start_time) || empty($end_time)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: add_booking.php");
        exit();
    }

    // Check for overlapping bookings
    $overlap_sql = "SELECT COUNT(*) AS count FROM lab_bookings 
                    WHERE lab_id = ? AND booking_date = ? 
                    AND ((start_time < ? AND end_time > ?) OR 
                         (start_time < ? AND end_time > ?) OR 
                         (start_time >= ? AND end_time <= ?))";
    
    $stmt = $conn->prepare($overlap_sql);
    $stmt->bind_param("isssssss", $lab_id, $booking_date, $end_time, $end_time, $start_time, $start_time, $start_time, $end_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $_SESSION['error'] = "The selected time slot is already booked!";
        header("Location: add_bookings.php");
        exit();
    }

    // Insert booking into database
    $insert_sql = "INSERT INTO lab_bookings (user_id, lab_id, booking_date, start_time, end_time, status) 
                   VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iisss", $user_id, $lab_id, $booking_date, $start_time, $end_time);

    if ($stmt->execute()) {
        // Notify Admin via Email
        $admin_email = "admin@example.com"; // Change to actual admin email
        $subject = "New Lab Booking Request";
        $message = "A new lab booking request has been submitted.\n\n";
        $message .= "User ID: $user_id\n";
        $message .= "Lab ID: $lab_id\n";
        $message .= "Booking Date: $booking_date\n";
        $message .= "Time: $start_time - $end_time\n\n";
        $message .= "Approve or Reject the request: http://yourwebsite.com/manage_lab_bookings.php";
        
        // Email Headers
        $headers = "From: noreply@yourwebsite.com\r\n";
        $headers .= "Reply-To: noreply@yourwebsite.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        mail($admin_email, $subject, $message, $headers);

        $_SESSION['success'] = "Lab booking request submitted successfully! Admin will review your request.";
        header("Location: manage_bookings.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to book the lab. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Book a Laboratory</h2>

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

        <form method="POST" action="add_bookings.php" class="border p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="lab_id" class="form-label">Select Lab</label>
                <select name="lab_id" id="lab_id" class="form-select" required>
                    <option value="">-- Select Lab --</option>
                    <?php
                    $labs_sql = "SELECT lab_id, lab_name FROM laboratories ORDER BY lab_name";
                    $labs_result = $conn->query($labs_sql);
                    while ($lab = $labs_result->fetch_assoc()) {
                        echo "<option value='{$lab['lab_id']}'>" . htmlspecialchars($lab['lab_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="booking_date" class="form-label">Booking Date</label>
                <input type="date" name="booking_date" id="booking_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" name="end_time" id="end_time" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit Booking Request</button>
        </form>
        
        <div class="mt-3">
            <a href="manage_bookings.php" class="btn btn-secondary">Back to Bookings</a>
        </div>
    </div>
</body>
</html>
