window.addEventListener('DOMContentLoaded', (event) => {
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0'; 
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 500); 
        }, 4000); 
    }
});