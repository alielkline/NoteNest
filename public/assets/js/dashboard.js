const toggleBtn = document.getElementById('toggle-notes-btn');
    const extraNotes = document.querySelectorAll('.extra-note');

    toggleBtn?.addEventListener('click', function () {
        const isHidden = extraNotes[0]?.classList.contains('d-none');
        extraNotes.forEach(note => note.classList.toggle('d-none'));

        toggleBtn.textContent = isHidden ? 'View Less' : 'View More';
    });
