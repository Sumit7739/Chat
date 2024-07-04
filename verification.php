<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = trim($_POST['otp']);

    if (empty($entered_otp)) {
        echo 'Please enter the OTP.';
        exit;
    }

    // Fetch stored OTP for the user
    $email = $_SESSION['signup_email']; // Retrieve stored email from session
    $query = "SELECT otp FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $stored_otp = $user['otp'];

            if ($entered_otp == $stored_otp) {
                // Update user verification status or proceed with further actions
                echo 'Verification successful!';
                header("Location: success.html");
                // Example: Update verification status in database
                $update_query = "UPDATE users SET verified = 1 WHERE email = ?";
                $stmt_update = $conn->prepare($update_query);
                $stmt_update->bind_param('s', $email);
                $stmt_update->execute();
                $stmt_update->close();
            } else {
                echo 'Invalid OTP. Please try again.';
            }
        } else {
            echo 'User not found or multiple users with the same email.';
        }
        $stmt->close();
    } else {
        echo 'Database query failed: ' . $conn->error;
    }
    $conn->close();
} else {
    echo 'Invalid request method.';
}
?>
