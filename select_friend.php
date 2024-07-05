<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch friends from the database
$query = "
    SELECT u.id, u.username, u.full_name 
    FROM users u 
    JOIN friends f ON (u.id = f.friend_id AND f.user_id = ?) OR (u.id = f.user_id AND f.friend_id = ?)
    WHERE u.id != ?
";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('iii', $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $friends = [];
    while ($row = $result->fetch_assoc()) {
        $friends[] = $row;
    }
    $stmt->close();
} else {
    echo "Failed to fetch friends: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Friend</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.min.css">
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f9f9f9;
            padding-top: 40px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .friend {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .friend:last-child {
            border-bottom: none;
        }
        .friend p {
            font-size: 16px;
            margin: 0;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Friend</h2>
        <div class="friends">
            <?php foreach ($friends as $friend): ?>
                <div class="friend">
                    <p><strong>Username:</strong> <?= htmlspecialchars($friend['username']) ?></p>
                    <p><strong>Full Name:</strong> <?= htmlspecialchars($friend['full_name']) ?></p>
                    <form action="chat.php" method="get">
                        <input type="hidden" name="friend_id" value="<?= $friend['id'] ?>">
                        <button type="submit" class="btn">Chat</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
