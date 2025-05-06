<?php
require_once __DIR__ . '/../../config/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
require_once __DIR__ . '/../../controllers/ClassroomController.php'; 
    
$controller = new ClassroomController();
$data = $controller->viewSubjects();
$classroom = $data['classroom'];
$is_member = $data['is_member'];
$subjects = $data['subjects'];

$classroom_name = $classroom['name'];
$classroom_desc = $classroom['description'];
$classroom_date = $classroom['created_at'];
$classroom_invCode = $classroom['invite_code'];
$classroom_visibility = $classroom['visibility'];

$classroom_id = $_GET['classroom_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($classroom_name) ?> - Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/subjetcs.css">
</head>
<body>
<?php include '../partials/navbar.php'; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-message" id="error-message">
        <div><?= htmlspecialchars($_SESSION['error']); ?></div>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-message" id="success-message">
        <div><?= htmlspecialchars($_SESSION['success']); ?></div>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="container my-4">
    <div class="row mb-1">
        <div class="col-12">
            <p class="text-muted">
                <a href="classrooms.php" class="classroom-link">Classrooms</a> / 
                <strong><?= htmlspecialchars($classroom_name) ?></strong>
            </p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><?= htmlspecialchars($classroom_name) ?> - Subjects</h2>

        <?php if ($classroom['creator_id'] == $user_id): ?>
            <div class="d-flex gap-2">
                <button class="btn btn-purple text-white" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
                    <i class="bi bi-plus-lg me-1"></i> New Subject
                </button>
                <button class="btn custom-grey-btn" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    <i class="bi bi-gear me-1"></i> Settings
                </button>
            </div>
        <?php endif; ?>

        <?php if ($is_member && $classroom['creator_id'] != $user_id): ?>
            <form action="../../controllers/ClassroomController.php?action=leave" method="POST" class="ms-2">
                <input type="hidden" name="classroom_id" value="<?= $classroom_id ?>">
                <button class="btn btn-outline-danger" type="submit">
                    <i class="bi bi-box-arrow-left me-1"></i> Leave Classroom
                </button>
            </form>
        <?php elseif (!$is_member): ?>
            <form action="../../controllers/ClassroomController.php?action=join" method="POST" class="ms-2">
                <input type="hidden" name="classroom_id" value="<?= $classroom_id ?>">
                <button class="btn btn-outline-success" type="submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Join Classroom
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <p class="desc"><?= htmlspecialchars($classroom_desc) ?></p>
        </div>
    </div>

    <div class="row g-4 card-grid">
        <?php if (count($subjects) > 0): ?>
            <?php foreach ($subjects as $subject): ?>
                <div class="col-md-6 col-lg-4 d-flex">
                    <div class="card card-custom p-3 flex-fill position-relative">
                        <h5 class="fw-semibold">
                            <i class="bi bi-folder" style="color: #8b5cf6;"></i>
                            <?= htmlspecialchars($subject['subject_name']) ?>
                        </h5>
                        <p class="text-muted"><?= htmlspecialchars($subject['subject_desc']) ?></p>
                        <div class="d-flex justify-content-between text-muted small mt-auto">
                            <span class="notes-count">📝 <?= $subject['notes'] ?> notes</span>
                            <a href="../notes/subject_notes.php?subject_id=<?= $subject['subject_id'] ?>&classroom_id=<?= $classroom_id ?>" class="view-notes-btn">
                                View Notes →
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No subjects available for this classroom.</p>
        <?php endif; ?>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="createSubjectModal" tabindex="-1" aria-labelledby="createSubjectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createSubjectModalLabel">Create a New Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../../controllers/SubjectController.php" method="POST">
                            <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($classroom_id) ?>">
                            <input type="hidden" name="create_subject" value="1">
                            <div class="mb-3">
                                <label for="subjectName" class="form-label">Subject Name</label>
                                <input type="text" class="form-control" id="subjectName" name="subject_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="subjectDesc" class="form-label">Subject Description</label>
                                <input type="text" class="form-control" id="subjectDesc" name="subject_desc" required>
                            </div>
                            <button type="submit" class="btn btn-outline-purple">Create Subject</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Edit Classroom Form -->
                <form id="editForm" action="../../controllers/ClassroomController.php?action=update" method="POST">
                    <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($classroom_id) ?>">

                    <div class="mb-3">
                        <label for="classroomName" class="form-label">Classroom Name</label>
                        <input type="text" class="form-control" id="classroomName" name="classroomName" value="<?= htmlspecialchars($classroom_name) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="inviteCode" class="form-label">Invite Code:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="inviteCode" value="<?= htmlspecialchars($classroom_invCode); ?>" readonly>
                            <button class="btn btn-purple text-white" type="button" onclick="copyInviteCode()">Copy</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description:</label>
                        <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($classroom_desc); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="visibility" class="form-label">Visibility</label>
                        <select class="form-select" id="visibility" name="visibility" required>
                            <option value="public" <?= ($classroom_visibility === 'public') ? 'selected' : ''; ?>>Public</option>
                            <option value="private" <?= ($classroom_visibility === 'private') ? 'selected' : ''; ?>>Private</option>
                        </select>
                    </div>
                </form>

                <!-- Buttons Row (Save Changes and Delete) -->
                <div class="d-flex justify-content-between mt-4">
                    <!-- Save Changes Button Inside the Form -->
                    <button form="editForm" type="submit" class="btn btn-outline-purple">Save Changes</button>

                    <!-- Delete Classroom Form + Button -->
                    <form action="../../controllers/ClassroomController.php?action=delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this classroom? This action cannot be undone.');">
                        <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($classroom_id) ?>">
                        <button type="submit" class="btn btn-danger">Delete Classroom</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../public/assets/js/error.js"></script>
<script src="../../public/assets/js/success.js"></script>
<script src="../../public/assets/js/copy.js"></script>
</body>
</html>