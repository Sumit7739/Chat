<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Feed</title>
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
        .post-form {
            margin-bottom: 20px;
        }
        .post-form textarea {
            width: 100%;
            height: 100px;
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
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input {
            width: calc(100% - 40px);
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
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
        <h2>User Feed</h2>
        
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search for friends by username">
            <button class="btn" onclick="searchUser()">Search</button>
        </div>

        <form class="post-form" action="post.php" method="post">
            <textarea name="content" placeholder="What's on your mind?" required></textarea>
            <button type="submit" class="btn">Post</button>
        </form>

        <div class="posts">
            <?php
            session_start();
            include 'db.php';

            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];

                // Query to fetch posts
                $query = "SELECT users.username, posts.content, posts.created_at 
                          FROM posts 
                          JOIN users ON posts.user_id = users.id 
                          ORDER BY posts.created_at DESC";

                if ($result = $conn->query($query)) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='post'>";
                        echo "<p><strong>" . htmlspecialchars($row['username']) . "</strong></p>";
                        echo "<p>" . htmlspecialchars($row['content']) . "</p>";
                        echo "<p><small>" . $row['created_at'] . "</small></p>";
                        echo "</div>";
                    }
                } else {
                    echo "Failed to fetch posts: " . $conn->error;
                }
            } else {
                echo "Please log in to view the feed.";
            }
            ?>
        </div>
    </div>

    <script>
        function searchUser() {
            var query = document.getElementById('search').value;
            if (query) {
                window.location.href = 'search.php?query=' + query;
            }
        }
    </script>
</body>
</html>
