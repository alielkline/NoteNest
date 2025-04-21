function showTab(tabId) {
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(content => content.classList.add('d-none'));

  document.getElementById(tabId).classList.remove('d-none');
  document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');
}

function submitForm() {
  document.getElementById('photoForm').submit();
}