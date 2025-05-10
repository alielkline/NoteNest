// real-time password checking
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirmPassword');
const errorElement = document.getElementById('passwordError');

confirmPasswordInput.addEventListener('input', function() {
    const password = passwordInput.value;
    const confirmPassword = this.value;

    if (confirmPassword && password !== confirmPassword) {
        errorElement.style.display = 'block';
        confirmPasswordInput.style.borderColor = '#dc3545'; 
    } else if (confirmPassword && password === confirmPassword) {
        errorElement.style.display = 'none';
        confirmPasswordInput.style.borderColor = '#28a745';
    } else {
        errorElement.style.display = 'none';
        confirmPasswordInput.style.borderColor = ''; 
    }
});
