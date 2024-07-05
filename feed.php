<?php
session_start();
include 'db.php'; // Include your database connection

$posts = [];
$error = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Query to fetch posts
    $query = "SELECT users.username, posts.content, posts.created_at 
              FROM posts 
              JOIN users ON posts.user_id = users.id 
              ORDER BY posts.created_at DESC";

    if ($result = $conn->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    } else {
        $error = "Failed to fetch posts: " . $conn->error;
    }
} else {
    $error = "Please log in to view the feed.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Feed</title>
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
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .post-form textarea {
            min-height: 100px;
            border-radius: 4px;
            padding: 10px;
        }

        .post {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .post:last-child {
            border-bottom: none;
        }

        .post p {
            font-size: 16px;
            margin: 0;
        }

        .search-bar input {
            width: calc(100% - 40px);
            padding: 10px;
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            border: none;
            /* padding: 10px 20px; */
            border-radius: 16px;
            margin-bottom: 10px;
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
        <h4>User Feed</h4>
        <a href="profile.php" class="btn">Profile</a>
        <br> <hr>
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search for friends by username">
            <button class="btn" onclick="searchUser()">Search</button>
        </div>

        <form class="post-form" action="post.php" method="post">
            <textarea name="content" placeholder="What's on your mind?" required></textarea>
            <button type="submit" class="btn">Post</button>
        </form>

        <div class="posts">
            <?php if ($error) : ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php else : ?>
                <?php foreach ($posts as $post) : ?>
                    <div class="post">
                        <p><strong><?php echo htmlspecialchars($post['username']); ?></strong></p>
                        <p><?php echo htmlspecialchars($post['content']); ?></p>
                        <p><small><?php echo htmlspecialchars($post['created_at']); ?></small></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function searchUser() {
            var query = document.getElementById('search').value;
            if (query) {
                window.location.href = 'search.php?query=' + encodeURIComponent(query);
            }
        }
    </script>
</body>

</html>