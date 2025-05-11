// script.js

document.addEventListener('DOMContentLoaded', () => {
    console.log('Script.js loaded successfully!');
    
    // Contoh interaksi kecil: tombol login disable setelah diklik
    const loginForm = document.querySelector('.login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            const loginBtn = loginForm.querySelector('.login-btn');
            if (loginBtn) {
                loginBtn.disabled = true;
                loginBtn.innerText = 'Logging in...';
            }
        });
    }
});

