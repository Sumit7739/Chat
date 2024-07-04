<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .profile-info {
            margin-bottom: 20px;
            text-align: left;
        }
        .profile-info p {
            margin-bottom: 10px;
            font-size: 16px;
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
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>
        <div class="row">
            <div class="three columns">
                <?php
                session_start();
                include 'db.php'; // Include your database connection

                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];

                    // Query to fetch user details
                    $query = "SELECT full_name, username, email, profile_image FROM users WHERE id = ?";

                    if ($stmt = $conn->prepare($query)) {
                        $stmt->bind_param('i', $user_id);
                        $stmt->execute();
                        $stmt->store_result();

                        if ($stmt->num_rows > 0) {
                            $stmt->bind_result($fullname, $username, $email, $profile_image);
                            $stmt->fetch();

                            echo "<img class='profile-image u-full-width' src='$profile_image' alt='Profile Picture'>";
                            echo "<div class='profile-info'>";
                            echo "<p><strong>Full Name:</strong> $fullname</p>";
                            echo "<p><strong>Username:</strong> $username</p>";
                            echo "<p><strong>Email:</strong> $email</p>";
                            echo "</div>";
                            echo "<a href='change_password.php' class='btn'>Change Password</a>";
                            echo "<a href='image_add.php' class='btn btn-secondary'>Update Profile</a>";
                            echo "<a href='logout.php' class='btn btn-danger'>Logout</a>";
                        } else {
                            echo "<p>User not found.</p>";
                        }

                        $stmt->close();
                    } else {
                        echo "<p>Database query failed: " . $conn->error . "</p>";
                    }
                } else {
                    echo "<p>Please log in to view your profile.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
