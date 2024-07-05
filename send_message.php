<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['content']) && !empty($data['content']) && isset($data['friend_id'])) {
        $content = $conn->real_escape_string($data['content']);
        $friend_id = intval($data['friend_id']);

        $query = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('iis', $user_id, $friend_id, $content);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Message content is empty or friend ID is missing']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
