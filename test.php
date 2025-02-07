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
                    <div class="error-message" id="nameError"></div>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <div class="error-message" id="emailError"></div>
                </div>
            </div>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" placeholder="Your message" required></textarea>
            <div class="error-message" id="messageError"></div>

            <button type="submit">Send Message</button>
        </form>
    </div>
    <script>
        class FeedbackForm {
            constructor(formId, responseMessageId, messagesContainerId) {
                this.form = document.getElementById(formId);
                this.responseMessage = document.getElementById(responseMessageId);
                this.messagesContainer = document.getElementById(messagesContainerId);
                this.init();
            }

            init() {
                this.form.addEventListener('submit', (event) => this.handleSubmit(event));
                window.onload = () => this.fetchMessages();
            }

            async handleSubmit(event) {
                event.preventDefault(); // Prevent default form submission
                this.clearErrors(); // Clear previous error messages

                const formData = new FormData(this.form);
                let isValid = true;

                // Validate fields
                if (!this.validateField('name', 'nameError')) isValid = false;
                if (!this.validateField('email', 'emailError')) isValid = false;
                if (!this.validateField('message', 'messageError')) isValid = false;

                if (isValid) {
                    try {
                        const response = await fetch('submit.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        this.displayResponse(data);
                        if (data.success) {
                            this.form.reset(); // Reset form
                            this.fetchMessages(); // Fetch messages after successful submission
                        }
                    } catch (error) {
                        console.error('Ошибка:', error);
                    }
                }
            }

            validateField(fieldId, errorId) {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(errorId);
                if (!field.value.trim()) {
                    errorElement.textContent = 'Это поле обязательно для заполнения.';
                    return false;
                }
                errorElement.textContent = ''; // Clear error message
                return true;
            }

            clearErrors() {
                const errorElements = document.querySelectorAll('.error-message');
                errorElements.forEach(element => element.textContent = '');
            }

            displayResponse(data) {
                if (data.success) {
                    this.responseMessage.innerHTML = `<div class="success">${data.message}</div>`;
                } else {
                    this.responseMessage.innerHTML = `<div class="error">${data.message}</div>`;
                }
            }

            async fetchMessages() {
                try {
                    const response = await fetch('fetch_data.php');
                    const data = await response.json();
                    this.updateMessages(data);
                } catch (error) {
                    console.error('Ошибка:', error);
                }
            }

            updateMessages(data) {
                this.messagesContainer.innerHTML = ''; // Clear previous messages
                if (data.success) {
                    data.data.forEach(message => {
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add('message');
                        messageDiv.innerHTML = `<strong>Имя:</strong> <p>${message.name}</p>
                            <strong>Email:</strong> <p>${message.email}</p>
                            <strong>Сообщение:</strong> <p>${message.message.replace(/\n/g, '<br>')}</p>`;
                        this.messagesContainer.appendChild(messageDiv);
                    });
                } else {
                    this.messagesContainer.innerHTML = `<div class="error">${data.message}</div>`;
               }
            }
        }

        // Initialize the FeedbackForm class
        new FeedbackForm('feedbackForm', 'responseMessage', 'messagesContainer');
    </script>
</body>

</html>
