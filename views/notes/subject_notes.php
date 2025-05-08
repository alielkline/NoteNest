<?php
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../controllers/NoteController.php';

$controller = new NoteController();
$data = $controller->showSubjectNotes();

$classroom = $data['classroom'];
$subject = $data['subject'];
$notes = $data['notes'];
$user_id = $data['user_id'];
$classroom_id = $_GET['classroom_id'];
$subject_id = $_GET['subject_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($subject['subject_name']) ?> - Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/main.css">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/subject_notes.css">
</head>
<body>
    <?php include '../partials/navbar.php'; ?>

    <!-- Error and Success Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message" id="error-message">
            <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message" id="success-message">
            <div><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="container my-4">
        <!-- Breadcrumb and Subject Title -->
        <div class="row mb-1">
            <div class="col-12">
                <p class="text-muted">
                    <a href="../pages/subjects.php?classroom_id=<?= $classroom_id ?>" class="subject-link">Subjects</a> / 
                    <strong><?= htmlspecialchars($subject['subject_name']) ?></strong>
                </p>
            </div>
        </div>

        <!-- Heading with Buttons (for classroom creator) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0"><?= htmlspecialchars($subject['subject_name']) ?> - Notes</h2>
            <?php if ($classroom['creator_id'] == $user_id): ?>
                <div class="d-flex gap-2">
                    <a href="../notes/create_note.php?subject_id=<?= $subject_id ?>&classroom_id=<?= $classroom_id?>" class="btn btn-purple text-white">
                        <i class="bi bi-plus-lg me-1"></i> New Note
                    </a>
                    <button class="btn custom-grey-btn" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="bi bi-gear me-1"></i> Settings
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Classroom Description -->
        <div class="row mb-4">
            <div class="col-12">
                <p class="desc"><?= htmlspecialchars($subject['subject_desc']) ?></p>
            </div>
        </div>

        <!-- Notes Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <div class="text-center my-4">
                <h5 class="d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-book text-purple me-2"></i> Subject - Notes
                </h5>
            </div>
        </div>

        <!-- Notes List -->
        <div class="row g-4 justify-content-center" id="notes-container">
            <?php foreach ($notes as $index => $note): ?>
                <div class="col-12 note-card <?= $index >= 4 ? 'd-none extra-note' : '' ?>">
                    <div class="card custom-style card-custom p-3 d-flex flex-column position-relative w-100">
                        <h5 class="fw-semibold mb-2"><?= htmlspecialchars($note['title']) ?></h5>
                        <p class="text-muted mb-3"><?= htmlspecialchars(mb_strimwidth($note['content'], 0, 120, '...')) ?></p>
                        <div class="mt-auto d-flex justify-content-between align-items-center text-muted small">
                            <span>📅 <?= date('M d, Y', strtotime($note['upload_date'])) ?></span>
                            <span>👍 <?= $note['likes'] ?? 0 ?> | 🔖 <?= $note['bookmarkes'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- View More Button -->
        <?php if (count($notes) > 4): ?>
            <div class="text-center mt-3">
                <button class="btn btn-outline-dark" id="toggle-notes-btn">View More</button>
            </div>
        <?php endif; ?>

        <!-- No Notes Found Alert -->
        <?php if (empty($notes)): ?>
            <div class="col-12">
                <div class="alert alert-secondary text-center" role="alert">
                    No notes found.
                </div>
            </div>
        <?php endif; ?>

        <!-- Settings Modal -->
        <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="settingsModalLabel">Subject Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Edit Subject Form -->
                        <form id="editSubjectForm" action="../../controllers/SubjectController.php" method="POST">
                            <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['subject_id']) ?>">
                            <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($classroom['classroom_id']) ?>">
                            <input type="hidden" name="update_subject" value="1"> 

                            <div class="mb-3">
                                <label for="subjectName" class="form-label">Subject Name</label>
                                <input type="text" class="form-control" id="subjectName" name="subjectName" value="<?= htmlspecialchars($subject['subject_name']) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="subjectDesc" class="form-label">Description</label>
                                <input type="text" class="form-control" id="subjectDesc" name="subjectDesc" value="<?= htmlspecialchars($subject['subject_desc']) ?>">
                            </div>
                        </form>

                        <!-- Buttons Row -->
                        <div class="d-flex justify-content-between mt-4">
                            <button form="editSubjectForm" type="submit" class="btn btn-outline-purple">Save Changes</button>

                            <form action="../../controllers/SubjectController.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this subject? This action cannot be undone.');">
                                <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['subject_id']) ?>">
                                <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($classroom['classroom_id']) ?>">
                                <input type="hidden" name="delete_subject" value="1"> <!-- Add this -->
                                <button type="submit" class="btn btn-danger">Delete Subject</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/error.js"></script>
    <script src="../../public/assets/js/success.js"></script>
</body>
</html>
