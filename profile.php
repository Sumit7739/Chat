<?php
session_start();
include 'db.php'; // Include your database connection

$fullname = $username = $email = $profile_image = $error = '';

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
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    } else {
        $error = "Database query failed: " . $conn->error;
    }
} else {
    $error = "Please log in to view your profile.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .profile-info p {
            margin-bottom: 10px;
            font-size: 16px;
        }
        .btn {
            margin-top: 20px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h4>User Profile</h4>
        <div class="row">
            <div class="col s12 m4">
                <?php if ($error): ?>
                    <p><?php echo $error; ?></p>
                <?php else: ?>
                    <img class="profile-image circle responsive-img" src="<?php echo $profile_image; ?>" alt="Profile Picture">
                    <div class="profile-info">
                        <p><strong>Full Name:</strong> <?php echo $fullname; ?></p>
                        <p><strong>Username:</strong> <?php echo $username; ?></p>
                        <p><strong>Email:</strong> <?php echo $email; ?></p>
                    </div>
                    <a href="feed.php" class="btn waves-effect waves-light">Search Friend</a>
                    <a href="select_friend.php" class="btn waves-effect waves-light grey">Chat</a>
                    <a href="logout.php" class="btn waves-effect waves-light red">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
