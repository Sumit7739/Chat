<?php
include 'db.php';

$sql = "SELECT user, message, timestamp FROM messages ORDER BY timestamp DESC";
$result = $conn->query($sql);

$messages = array();
while($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

$conn->close();
?>
