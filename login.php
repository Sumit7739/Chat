<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usernameOrEmail = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($usernameOrEmail) || empty($password)) {
        echo 'Please fill in all fields.';
        exit;
    }

    // Check if the input is an email or username
    if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
        $query = "SELECT * FROM users WHERE email = ?";
    } else {
        $query = "SELECT * FROM users WHERE username = ?";
    }

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                echo 'success';
            } else {
                echo 'Incorrect password.';
            }
        } else {
            echo 'No account found with that username or email.';
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
