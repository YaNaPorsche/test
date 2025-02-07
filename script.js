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
            event.preventDefault();
            this.clearErrors();
            const formData = new FormData(this.form);
            let isValid = true;

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
                        this.form.reset();
                        this.fetchMessages();
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
            errorElement.textContent = '';
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
            this.messagesContainer.innerHTML = '';
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

    new FeedbackForm('feedbackForm', 'responseMessage', 'messagesContainer');