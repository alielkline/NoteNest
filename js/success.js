window.addEventListener('DOMContentLoaded', (event) => {
    const successMessage = document.getElementById('success-message');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0'; 
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 500); 
        }, 4000); 
    }
});