<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Prevent adding self as friend
    if ($user_id !== $friend_id) {
        // Check if the friendship already exists
        $check_query = "SELECT * FROM friends WHERE user_id = ? AND friend_id = ?";
        if ($stmt = $conn->prepare($check_query)) {
            $stmt->bind_param('ii', $user_id, $friend_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                // Add the friendship
                $insert_query = "INSERT INTO friends (user_id, friend_id) VALUES (?, ?)";
                if ($stmt = $conn->prepare($insert_query)) {
                    $stmt->bind_param('ii', $user_id, $friend_id);
                    if ($stmt->execute()) {
                        header('Location: search.php?query=' . urlencode($_GET['query']) . '&status=success');
                        exit();
                    } else {
                        echo "Failed to add friend: " . $stmt->error;
                    }
                } else {
                    echo "Database query failed: " . $conn->error;
                }
            } else {
                echo "You are already friends with this user.";
            }

            $stmt->close();
        } else {
            echo "Database query failed: " . $conn->error;
        }
    } else {
        echo "You cannot add yourself as a friend.";
    }
} else {
    echo "Invalid request.";
}
?>
