function showTab(tabId) {
  // Remove active class from all tabs and contents
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

  // Add active to selected tab and content
  document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
  document.getElementById(tabId).classList.add('active');
}