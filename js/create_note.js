
    const fileInput = document.getElementById('attachment');
    const fileNameDisplay = document.getElementById('file-name');

    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = "Selected file: " + fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = "";
        }
    });
