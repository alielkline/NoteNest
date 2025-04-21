function showTab(tabId) {
  // Remove active class from all tabs and contents
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

  // Add active to selected tab and content
  document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
  document.getElementById(tabId).classList.add('active');
}

const fileInput = document.getElementById('upload');
const photoForm = document.getElementById('photoForm');

// When a file is selected, submit the form
fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            photoForm.submit();
        }
});