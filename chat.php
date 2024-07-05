<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['friend_id'])) {
    header('Location: select_friend.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = intval($_GET['friend_id']);

// Fetch friend's data
$query = "SELECT username, full_name FROM users WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $friend_id);
    $stmt->execute();
    $stmt->bind_result($friend_username, $friend_fullname);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Failed to fetch friend data: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?= htmlspecialchars($friend_username) ?></title>
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
            height: 80%;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .chat-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            height: 600px;
            overflow-y: scroll;
        }

        .chat-message {
            padding: 3px;
            padding-bottom: 0px;
            margin: 5px 0;
            border-radius: 8px;
            background-color: #f1f1f1;
        }

        .chat-message.me {
            background-color: #007bff;
            color: #fff;
            text-align: right;
        }

        .message-form {
            display: flex;
            margin-top: 10px;
        }

        .message-form textarea {
            flex: 1;
            border-radius: 4px;
            padding: 10px;
            margin-right: 10px;
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 0px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }


        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            border: none;
            /* width: auto; */
            padding: 0px 20px;
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
        <h4><?= htmlspecialchars($friend_fullname) ?>
            <button class="delete-btn" id="deleteMessagesBtn">Delete</button>
        </h4>

        <div class="chat-box" id="chatBox">
            <!-- Chat messages will be loaded here dynamically -->
        </div>

        <form class="message-form" id="messageForm">
            <textarea id="messageContent" placeholder="Type your message" required></textarea>
            <button type="submit" class="btn">Send</button>
        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatBox = document.getElementById('chatBox');
            const messageForm = document.getElementById('messageForm');
            const messageContent = document.getElementById('messageContent');
            const friendId = <?= $friend_id ?>;

            function fetchMessages() {
                fetch('fetch_messages.php?friend_id=' + friendId)
                    .then(response => response.json())
                    .then(data => {
                        chatBox.innerHTML = '';
                        data.messages.forEach(msg => {
                            const messageElement = document.createElement('div');
                            messageElement.classList.add('chat-message');
                            if (msg.sender_id === data.user_id) {
                                messageElement.classList.add('me');
                            }
                            messageElement.innerHTML = `<p>${msg.content} <br> <small>${msg.created_at}</small></p>`;
                            chatBox.appendChild(messageElement);
                        });
                        chatBox.scrollTop = chatBox.scrollHeight;
                    });
            }

            messageForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const content = messageContent.value;
                fetch('send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            content,
                            friend_id: friendId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageContent.value = '';
                            fetchMessages();
                        } else {
                            alert('Failed to send message');
                        }
                    });
            });

            deleteMessagesBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete all messages?')) {
                    fetch('delete_messages.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                friend_id: friendId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                fetchMessages();
                            } else {
                                alert('Failed to delete messages');
                            }
                        });
                }
            });

            setInterval(fetchMessages, 3000); // Refresh messages every 3 seconds
            fetchMessages(); // Initial fetch
        });
    </script>
</body>

</html>