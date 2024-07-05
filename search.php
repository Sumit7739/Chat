<?php
session_start();
include 'db.php';

$users = [];
$error = '';

if (isset($_SESSION['user_id']) && isset($_GET['query'])) {
    $current_user_id = $_SESSION['user_id'];
    $query = $conn->real_escape_string($_GET['query']);

    // Query to search users by username
    $search_query = "SELECT id, username, full_name FROM users WHERE username LIKE ?";
    if ($stmt = $conn->prepare($search_query)) {
        $search_param = "%" . $query . "%";
        $stmt->bind_param('s', $search_param);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $fullname);
            while ($stmt->fetch()) {
                // Check if the user is already a friend
                $friend_check_query = "SELECT 1 FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
                if ($friend_check_stmt = $conn->prepare($friend_check_query)) {
                    $friend_check_stmt->bind_param('iiii', $current_user_id, $id, $id, $current_user_id);
                    $friend_check_stmt->execute();
                    $friend_check_stmt->store_result();

                    $is_friend = ($friend_check_stmt->num_rows > 0);
                    $friend_check_stmt->close();

                    $users[] = [
                        'id' => $id,
                        'username' => $username,
                        'fullname' => $fullname,
                        'is_friend' => $is_friend
                    ];
                } else {
                    $error = "Database query failed: " . $conn->error;
                    break; // Stop processing if there's an error
                }
            }
        } else {
            $error = "No users found.";
        }
        $stmt->close();
    } else {
        $error = "Database query failed: " . $conn->error;
    }
} else {
    $error = "Please log in to search for users.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            padding-top: 40px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            /* padding: 20px; */
        }
        .user {
            border-bottom: 1px solid #ddd;
            padding: 30px 10px;
        }
        .user:last-child {
            border-bottom: none;
        }
        .user p {
            font-size: 16px;
            margin: 0;
        }
        .btn {
            /* margin-top: 10px; */
            margin-bottom: 10px;
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            /* margin-bottom: 20px; */
        }
        .users {
            /* display: flex; */
            /* flex-wrap: wrap; */
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="header">Search Results</h3>
        <a href="feed.php" class="btn waves-effect waves-light">Back</a>
        <div class="users">
            <?php if ($error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <div class="user card-panel hoverable">
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
                        <?php if ($user['id'] != $current_user_id): ?>
                            <?php if ($user['is_friend']): ?>
                                <button class="btn waves-effect waves-light disabled">Already Friend</button>
                            <?php else: ?>
                                <form action="add_friend.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="friend_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn waves-effect waves-light">Add Friend</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
