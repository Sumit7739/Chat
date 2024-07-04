<?php
// add error report here

error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/autoload.php'; // Adjust the path as needed
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
        echo 'Please fill in all fields.';
        exit;
    }

    // Check if email already exists in database
    $query_check_email = "SELECT id FROM users WHERE email = ?";
    if ($stmt_check_email = $conn->prepare($query_check_email)) {
        $stmt_check_email->bind_param('s', $email);
        $stmt_check_email->execute();
        $stmt_check_email->store_result();

        if ($stmt_check_email->num_rows > 0) {
            $error_message = 'Email already exists. Please use a different email address.';
            $redirect_url = 'signup.html'; // Redirect back to signup page
            header("Location: error.php?message=" . urlencode($error_message) . "&redirect=" . urlencode($redirect_url));
            exit;
        }
    } else {
        echo 'Database query failed: ' . $conn->error;
        exit;
    }

    // Generate OTP
    $otp = rand(100000, 999999); // Generate a 6-digit OTP

    // Insert into database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (full_name, username, email, password, otp) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('sssss', $fullname, $username, $email, $hashed_password, $otp);
        if ($stmt->execute()) {
            // Send OTP to user's email using PHP Mailer
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  // Specify SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'srisinhasumit10@gmail.com'; // SMTP username
                $mail->Password = 'ggtbuofjfdmqcohr'; // SMTP password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                //Recipients
                $mail->setFrom('srisinhasumit10@gmail.com', 'BUZZ Team');
                $mail->addAddress($email, $fullname); // Add a recipient

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your BUZZ Account';
                $mail->Body    = "
                    <html>
                    <head>
                      <title>Verify Your BUZZ Account</title>
                    </head>
                    <body>
                      <p>Hi $fullname,</p>
                      <p>Thank you for creating an account with BUZZ! To complete your registration and ensure the security of your account, please verify your email address using the following One-Time Password (OTP):</p>
                      <p style='font-weight: bold;'>$otp</p>
                      <p>This code is valid for 5 minutes. Please enter it in the designated field on our website to complete your registration.</p>
                      <p>If you didn't request this verification, please ignore this email. Your account remains secure.</p>
                      <p>Thanks,<br />The BUZZ Team</p>
                    </body>
                    </html>";

                $mail->send();

                $_SESSION['signup_email'] = $email; // Store email in session for verification page
                echo 'success';
                header("Location: verification.html");
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo 'Failed to register user. Please try again later.';
        }
        $stmt->close();
    } else {
        echo 'Database query failed: ' . $conn->error;
    }
    $conn->close();
} else {
    echo 'Invalid request method.';
}
