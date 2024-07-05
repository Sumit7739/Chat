<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id']) && !empty($_POST['content'])) {
        $user_id = $_SESSION['user_id'];
        $content = $conn->real_escape_string($_POST['content']);

        $query = "INSERT INTO posts (user_id, content) VALUES (?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('is', $user_id, $content);
            if ($stmt->execute()) {
                header('Location: feed.php');
                exit();
            } else {
                echo "Failed to post content: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Database query failed: " . $conn->error;
        }
    } else {
        echo "Please log in and enter content to post.";
    }
} else {
    echo "Invalid request method.";
}
?>
