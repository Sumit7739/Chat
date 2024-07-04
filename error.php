<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .error-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .error-container h2 {
            color: #d9534f;
        }
        .error-container p {
            margin-bottom: 20px;
        }
        .error-container a {
            color: #fff;
            background-color: #337ab7;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .error-container a:hover {
            background-color: #286090;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h2>Oops, something went wrong!</h2>
        <p><?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Unknown error occurred.'; ?></p>
        <p><a href="<?php echo isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'javascript:history.back()'; ?>">Go back</a></p>
    </div>
</body>
</html>
