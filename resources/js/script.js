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