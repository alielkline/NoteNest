window.addEventListener('DOMContentLoaded', (event) => {
    const errorMessage = document.getElementById('error-message');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0'; 
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 500); 
        }, 4000); 
    }
});