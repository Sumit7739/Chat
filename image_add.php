<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile Picture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .profile-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .btn {
            color: #fff;
            background-color: #337ab7;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin: 0 10px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #286090;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Profile Picture</h2>
        <img class="profile-image" src="path_to_default_image" alt="Profile Picture">
        <form action="upload_image.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*" required>
            <br><br>
            <button type="submit" class="btn">Upload Image</button>
        </form>
        <br>
        <form action="change_image.php" method="POST">
            <button type="submit" class="btn">Change Image</button>
        </form>
        <br>
        <form action="remove_image.php" method="POST">
            <button type="submit" class="btn">Remove Image</button>
        </form>
    </div>
</body>
</html>
