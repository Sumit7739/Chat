<?php
session_start();
// add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file was uploaded without errors
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/profile/"; // Path to profile folder in root directory
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file size (max 5MB)
        if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
            echo "Sorry, your file is too large.";
            exit;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Update user's profile image path in database
            $image_path = "profile/" . basename($_FILES["image"]["name"]); // Relative path to store in database
            $user_id = $_SESSION['user_id']; // Example: Retrieve user ID from session

            $query_update_image = "UPDATE users SET profile_image = ? WHERE id = ?";
            if ($stmt_update_image = $conn->prepare($query_update_image)) {
                $stmt_update_image->bind_param('si', $image_path, $user_id);
                if ($stmt_update_image->execute()) {
                    echo "Profile image updated successfully.";
                } else {
                    echo "Failed to update profile image.";
                }
                $stmt_update_image->close();
            } else {
                echo "Database query failed: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "No file uploaded.";
    }
} else {
    echo "Invalid request method.";
}
?>
