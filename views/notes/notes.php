<?php
require_once __DIR__ . '/../../config/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../controllers/NoteController.php';

// Initialize the controller
$controller = new NoteController();
$data = $controller->showNotesPage();  // Get the data
$notes = $data['notes'];  // Extract notes
$classrooms = $data['classrooms'];  // Extract classrooms
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Browse and discover notes from all classrooms you have joined. Find, filter, and explore notes by classroom, subject, and likes.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/main.css">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/notes.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <title>Notes</title>
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="main-content p-4 w-100 container">
        <!-- Headers and Actions -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
            <div>
                <h2 class="mb-1">All Notes</h2>
                <p class="text-muted">Browse and discover notes from all classrooms you joined!</p>
            </div>
            <div class="p-3">
                <button class="btn-login border px-3 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#selectNoteContextModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg me-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                    </svg>
                    New Note
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="container d-flex flex-wrap align-items-center gap-3 my-3">
            <!-- Filter by Classroom -->
            <div>
                <select name="classroom_id" class="form-select d-inline-block w-auto btn-notes-dropdown">
                    <option value="all" <?= !isset($_GET['classroom_id']) || $_GET['classroom_id'] === 'all' ? 'selected' : '' ?>>All Classrooms</option>
                    <?php foreach ($classrooms as $class): ?>
                        <option value="<?= $class['classroom_id'] ?>" <?= (isset($_GET['classroom_id']) && $_GET['classroom_id'] == $class['classroom_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sort by Upload Date -->
            <div>
                <select name="sort_date" class="form-select d-inline-block w-auto btn-notes-dropdown">
                    <option value="newest" <?= (!isset($_GET['sort_date']) || $_GET['sort_date'] === 'newest') ? 'selected' : '' ?>>Newest</option>
                    <option value="oldest" <?= (isset($_GET['sort_date']) && $_GET['sort_date'] === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                </select>
            </div>

            <!-- Sort by Likes -->
            <div>
                <select name="sort_likes" class="form-select d-inline-block w-auto btn-notes-dropdown">
                    <option value="mostLiked" <?= (!isset($_GET['sort_likes']) || $_GET['sort_likes'] === 'mostLiked') ? 'selected' : '' ?>>Most Liked</option>
                    <option value="leastLiked" <?= (isset($_GET['sort_likes']) && $_GET['sort_likes'] === 'leastLiked') ? 'selected' : '' ?>>Least Liked</option>
                </select>
            </div>

            <!-- Submit -->
            <div>
                <button type="submit" class="btn custom-style">
                    <i class="bi bi-filter"></i> Apply
                </button>
            </div>
        </form>



        <!-- Notes List -->
        <div id="notes-container" class="container mt-4">
            <?php if (empty($notes)): ?>
                <div class="col-12">
                    <div class="alert alert-secondary text-center" role="alert">
                        No notes found.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($notes as $index => $note): ?>
                    <a href="../notes/single.php?note_id=<?= $note["note_id"] ?>" class="text-decoration-none text-reset">
                        <div class="col-12 mb-3 note-card <?= $index >= 4 ? 'd-none extra-note' : '' ?>">
                            <div class="card card-custom p-3 d-flex flex-column position-relative w-100">
                                <span>👤 <?= htmlspecialchars($note['username']) ?></span>
                                <h5 class="fw-semibold mb-2"><?= htmlspecialchars($note['title']) ?> <span class='subject-pill px-2 py-1'> <?= htmlspecialchars($note['subject_name']) ?></span></h5>
                                <p class="text-muted mb-3"><?= htmlspecialchars(mb_strimwidth($note['content'], 0, 30, '...')) ?></p>

                                <div class="mt-auto d-flex justify-content-between align-items-center text-muted small">
                                    <span>📅 <?= date('M d, Y', strtotime($note['upload_date'])) ?></span>
                                    <span><i class="bi bi-heart-fill like-heart me-1 purple-icon"></i> <?= $note['likes'] ?? 0 ?> </span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (count($notes) > 4): ?>
                <div class="text-center mt-3">
                    <button class="btn veiw-more-btn" id="toggle-notes-btn">View More</button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Create Note Context Modal -->
        <div class="modal fade" id="selectNoteContextModal" tabindex="-1" aria-labelledby="selectNoteContextModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectNoteContextModalLabel">Choose Classroom & Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../notes/create_note.php" method="GET">
                            <div class="mb-3">
                                <label for="classroomSelect" class="form-label">Select Classroom</label>
                                <select class="form-select" id="classroomSelect" name="classroom_id" required>
                                    <option value="">-- Select Classroom --</option>
                                    <?php foreach ($classrooms as $classroom): ?>
                                        <option value="<?= htmlspecialchars($classroom['classroom_id']) ?>">
                                            <?= htmlspecialchars($classroom['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="subjectSelect" class="form-label">Select Subject</label>
                                <select class="form-select" id="subjectSelect" name="subject_id" required disabled>
                                    <option value="">-- Select Subject --</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-outline-purple">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('classroomSelect').addEventListener('change', function() {
            const classroomId = this.value;
            const subjectSelect = document.getElementById('subjectSelect');

            subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';

            if (classroomId) {
                subjectSelect.disabled = false;

                fetch(`../../public/ajax/get_subjects.php?classroom_id=${classroomId}`)
                    .then(response => response.json())
                    .then(subjects => {
                        subjects.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.subject_id;
                            option.textContent = subject.subject_name;
                            subjectSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching subjects:', error));
            } else {
                subjectSelect.disabled = true;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const notesContainer = document.getElementById('notes-container');

            notesContainer.addEventListener('click', function(event) {
                if (event.target && event.target.id === 'toggle-notes-btn') {
                    const extraNotes = document.querySelectorAll('.extra-note');
                    const isHidden = extraNotes.length && extraNotes[0].classList.contains('d-none');

                    extraNotes.forEach(note => {
                        note.classList.toggle('d-none');
                    });

                    event.target.textContent = isHidden ? 'View Less' : 'View More';
                    if (isHidden && extraNotes.length > 0) {
                        extraNotes[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
        });
    </script>
<?php include '../partials/footer.php'; ?>