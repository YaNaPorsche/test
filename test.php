<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Feedback</title>
</head>

<body>
        <div id="responseMessage"></div>
        <div id="messagesContainer"></div> 
        <div class="container">
    <h2>Feedback</h2>
    <form id="feedbackForm">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" placeholder="Имя Фамилия Отчество" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
        </div>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="5" placeholder="Your message" required></textarea>

        <button type="submit">Send Message</button>
    </form>
</div>

<!-- <style>
    
</style> -->

    <script>
        document.getElementById('feedbackForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);

            fetch('submit.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseMessage = document.getElementById('responseMessage');
                if (data.success) {
                    responseMessage.innerHTML = `<div class="success">${data.message}</div>`;
                    this.reset(); // Reset form
                    fetchMessages(); // Fetch messages after successful submission
                } else {
                    responseMessage.innerHTML = `<div class="error">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        });

        // Function to fetch messages from the database
        function fetchMessages() {
            fetch('fetch_data.php')
            .then(response => response.json())
            .then(data => {
                const messagesContainer = document.getElementById('messagesContainer');
                messagesContainer.innerHTML = ''; // Clear previous messages
                if (data.success) {
                    data.data.forEach(message => {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message');
    messageDiv.innerHTML = `<strong>Имя:</strong> <p>${message.name}</p>
                            <strong>Email:</strong> <p>${message.email}</p>
                            <strong>Сообщение:</strong> <p>${message.message.replace(/\n/g, '<br>')}</p>`;
    messagesContainer.appendChild(messageDiv);
});
                } else {
                    messagesContainer.innerHTML = `<div class="error">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        }

        // Fetch messages when the page loads
        window.onload = fetchMessages;
    </script>
</body>

</html>
