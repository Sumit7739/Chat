<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
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
        .user {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .user:last-child {
            border-bottom: none;
        }
        .user p {
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
        <h2>Search Results</h2>
        <div class="users">
            <?php
            session_start();
            include 'db.php';

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
                            echo "<div class='user'>";
                            echo "<p><strong>Username:</strong> " . htmlspecialchars($username) . "</p>";
                            echo "<p><strong>Full Name:</strong> " . htmlspecialchars($fullname) . "</p>";
                            if ($id != $current_user_id) { // Prevent adding self as friend
                                echo "<form action='add_friend.php' method='post' style='display:inline-block;'>";
                                echo "<input type='hidden' name='friend_id' value='$id'>";
                                echo "<button type='submit' class='btn'>Add Friend</button>";
                                echo "</form>";
                            }
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No users found.</p>";
                    }
                    $stmt->close();
                } else {
                    echo "Database query failed: " . $conn->error;
                }
            } else {
                echo "Please log in to search for users.";
            }
            ?>
        </div>
    </div>
</body>
</html>
