document.addEventListener("DOMContentLoaded", function() {
    const messagesDiv = document.getElementById("messages");
    const form = document.getElementById("message-form");
    const userInput = document.getElementById("user");
    const messageInput = document.getElementById("message");

    let currentUser = "";

    function fetchMessages() {
        fetch("fetch_messages.php")
            .then(response => response.json())
            .then(data => {
                messagesDiv.innerHTML = "";
                data.forEach(message => {
                    const messageElement = document.createElement("div");
                    messageElement.classList.add("message");
                    if (message.user === currentUser) {
                        messageElement.classList.add("user");
                    } else {
                        messageElement.classList.add("other");
                    }
                    messageElement.textContent = `${message.user}: ${message.message}`;
                    messagesDiv.appendChild(messageElement);
                });
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            });
    }

    form.addEventListener("submit", function(event) {
        event.preventDefault();

        const user = userInput.value;
        const message = messageInput.value;
        currentUser = user;

        fetch("post_message.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `user=${user}&message=${message}`
        })
        .then(response => response.text())
        .then(data => {
            fetchMessages();
            messageInput.value = "";
        });
    });

    fetchMessages();
    setInterval(fetchMessages, 3000); // Refresh messages every 3 seconds
});
