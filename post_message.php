<?php
include 'db.php';

$user = $_POST['user'];
$message = $_POST['message'];

$sql = "INSERT INTO messages (user, message) VALUES ('$user', '$message')";
if ($conn->query($sql) === TRUE) {
    echo "New message posted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
