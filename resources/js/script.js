import 'boxicons/css/boxicons.min.css';

const container = document.querySelector('.container');
const register_button = document.querySelector('.register-btn');
const login_btn = document.querySelector('.login-btn');

if (register_button && container) {
    register_button.addEventListener('click', () => {
        container.classList.add('active');
    });
}

if (login_btn && container) {
    login_btn.addEventListener('click', () => {
        container.classList.remove('active');
    });
}


// Error message handling
document.addEventListener('DOMContentLoaded', function() {
    const errorMessages = document.querySelector('.error-messages');
    
    if (errorMessages) {
        // Auto-hide after 6 seconds
        setTimeout(() => {
            errorMessages.classList.add('hide');
            setTimeout(() => {
                errorMessages.style.display = 'none';
            }, 400); // Match the animation duration
        }, 6000);
        
        // Click to dismiss
        errorMessages.addEventListener('click', function(e) {
            if (e.target === this || e.target.closest('.error-messages::after')) {
                errorMessages.classList.add('hide');
                setTimeout(() => {
                    errorMessages.style.display = 'none';
                }, 400);
            }
        });
    }
});