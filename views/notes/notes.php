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
    <title>Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/notes.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include '../partials/navbar.php'; ?>

    <!-- Headers and Actions -->
    <div class="container d-flex justify-content-between mt-4">
        <div>
            <h2 class="header p-0 mb-0">All Notes</h2>
            <p class="text-muted">Browse and discover notes from all classrooms</p>
        </div>
        <div class="p-3">
            <button class="btn-login border px-3 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#selectNoteContextModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg me-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
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
    <div class="container">
        <?php if (empty($notes)): ?>
            <h4>No notes currently!</h4>
            <a href="classrooms.php" class="btn-no-notes p-1">Join a classroom!</a>
        <?php else: ?>
            <?php foreach ($notes as $note): ?>
                <?php
                    $excerpt = substr($note['content'], 0, 60) . '...';
                    $date = date("M d, Y", strtotime($note['upload_date']));
                ?>
                <div class="note-pane border mb-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="d-flex">
                                <p><?= htmlspecialchars($note['username']) ?></p>
                                <p class="mx-2">•</p>
                                <p class="text-muted"><?= $date ?></p>
                            </span>
                            <h4><?= htmlspecialchars($note['title']) ?></h4>
                            <p class="text-muted"><?= htmlspecialchars($excerpt) ?></p>
                            <p class="subject-pill px-2 py-1"><?= htmlspecialchars($note['subject_name']) ?></p>
                        </div>
                        <div class="d-flex mx-2 align-items-center">
                            <i class="bi bi-heart-fill like-heart me-1"></i>
                            <p class="like-counter mb-0"><?= $note['likes'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('classroomSelect').addEventListener('change', function () {
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

    </script>

</body>
</html>
