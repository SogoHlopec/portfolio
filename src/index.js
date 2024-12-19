const addError = (parent, message) => {
    try {
        const error = document.createElement('p');
        error.classList.add('error');
        error.textContent = message;
        parent.appendChild(error);
    } catch (error) {
        console.log(error);
    }
};

const clearErrors = (form) => {
    try {
        form.querySelectorAll('.error').forEach((error) => error.remove());
    } catch (error) {
        console.log(error);
    }
};

const sendData = async (url, data) => {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(data),
        });

        if (!response.ok) {
            throw new Error('Ошибка на сервере. Попробуйте позже.');
        }

        return response.json();
    } catch (error) {
        console.log(error);
    }
};

document.addEventListener('DOMContentLoaded', (e) => {
    try {
        const form = document.getElementById('form-request');

        if (!form) return;

        form.addEventListener('submit', async (e) => {
            clearErrors(form);

            let isValid = true;
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const seminarInput = document.getElementById('seminar');
            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            const seminar = seminarInput.value;

            if (!name) {
                addError(nameInput.parentNode, 'Введите имя');
                isValid = false;
            }
            if (!email) {
                addError(emailInput.parentNode, 'Введите корректный email');
                isValid = false;
            }
            if (!seminar) {
                addError(seminarInput.parentNode, 'Выберите семинар');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            } else {
                e.preventDefault();

                const data = {
                    action: 'send',
                    name: name,
                    email: email,
                    seminar: seminar,
                };

                const result = await sendData('send.php', data);
                console.log(result);
            }
        });
    } catch (error) {
        console.log(error);
    }
});
