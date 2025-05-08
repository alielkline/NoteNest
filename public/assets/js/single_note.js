function toggleLike(el) {
  const icon = el.querySelector('i');
  const noteId = el.getAttribute('data-note-id');
  const likeCount = document.getElementById('like-count');

  fetch('../../controllers/NoteController.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `action=toggleLike&note_id=${noteId}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      icon.classList.toggle('bi-heart');
      icon.classList.toggle('bi-heart-fill');
      likeCount.textContent = data.likes;
    } else {
      alert('Error toggling like');
    }
  });
}

function toggleBookmark(el) {
  const icon = el.querySelector('i');
  const noteId = el.getAttribute('data-note-id');

  fetch('../../controllers/NoteController.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `action=toggleBookmark&note_id=${noteId}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      icon.classList.toggle('bi-bookmark');
      icon.classList.toggle('bi-bookmark-fill');
    } else {
      alert('Error toggling bookmark');
    }
  });
}
