<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_GET['friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = intval($_GET['friend_id']);

    $query = "
        SELECT m.id, m.sender_id, m.receiver_id, m.content, m.created_at, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('iiii', $user_id, $friend_id, $friend_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        echo json_encode(['messages' => $messages, 'user_id' => $user_id]);
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Failed to fetch messages: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'User not logged in or friend ID not specified']);
}
?>
