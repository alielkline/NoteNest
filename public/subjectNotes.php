<?php
include '../includes/init.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure classroom ID is provided
if (!isset($_GET['classroom_id'])) {
    header("Location: ../public/classrooms.php");
    exit();
}

$classroom_id = $_GET['classroom_id'];

// Fetch the classroom details
$stmt = $pdo->prepare("SELECT * FROM classrooms WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);
$classroom = $stmt->fetch();

if (!$classroom) {
    header("Location: ../public/classrooms.php");
    exit();
}

// Ensure subject ID is provided
if (!isset($_GET['subject_id'])) {
    header("Location: ../public/dashboard.php");
    exit();
}

$subject_id = $_GET['subject_id'];

// Fetch the subject details
$stmt = $pdo->prepare("SELECT * FROM classroom_subjects WHERE subject_id = ?");
$stmt->execute([$subject_id]);
$subject = $stmt->fetch();

if (!$subject) {
    header("Location: ../public/dashboard.php");
    exit();
}

// Classroom and subject filter logic
$classroom_filter = isset($_GET['classroom_id']) && $_GET['classroom_id'] !== 'all' ? $_GET['classroom_id'] : null;
$subject_filter = isset($subject_id) ? $subject_id : null;

// Construct the query to fetch notes
$note_query = "
    SELECT cn.* 
    FROM classroom_notes cn 
    JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
    WHERE cs.classroom_id = ? AND cs.subject_id = ? AND cn.visibility = 'public'
    ORDER BY cn.likes DESC
";

$params = [$classroom_filter, $subject_filter];

// Prepare and execute the query
$note_stmt = $pdo->prepare($note_query);
$note_stmt->execute($params);
$notes = $note_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($subject['subject_name']) ?> - Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/subjectNotes.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

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
                    <a href="../public/subjects.php?classroom_id=<?= $classroom_id ?>" class="subject-link">Subjects</a> / 
                    <strong><?= htmlspecialchars($subject['subject_name']) ?></strong>
                </p>
            </div>
        </div>

        <!-- Heading with Buttons (for classroom creator) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0"><?= htmlspecialchars($subject['subject_name']) ?> - Notes</h2>
            <?php if ($classroom['creator_id'] == $user_id): ?>
                <div class="d-flex gap-2">
                    <a href="../public/create_note.php?subject_id=<?= $subject_id ?>&classroom_id=<?= $classroom_id?>" class="btn btn-purple text-white">
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
                        <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Edit Classroom Form -->
                        <form id="editForm" action="../includes/edit_classroom.php" method="POST">
                            <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id) ?>">
                            <div class="mb-3">
                                <label for="subjectName" class="form-label">Classroom Name</label>
                                <input type="text" class="form-control" id="classroomName" name="classroomName" value="<?= htmlspecialchars($classroom_name) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="inviteCode" class="form-label">Invite Code:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="inviteCode" value="<?= htmlspecialchars($classroom_invCode); ?>" readonly>
                                    <button class="btn btn-purple text-white" type="button" onclick="copyInviteCode()">Copy</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="describtion" class="form-label">Description:</label>
                                <input type="text" class="form-control" id="describtion" name="describtion" value="<?= htmlspecialchars($classroom_desc); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibility</label>
                                <select class="form-select" id="visibility" name="visibility">
                                    <option value="public" <?= ($classroom_visibility === 'public') ? 'selected' : ''; ?>>Public</option>
                                    <option value="private" <?= ($classroom_visibility === 'private') ? 'selected' : ''; ?>>Private</option>
                                </select>
                            </div>
                        </form>

                        <!-- Buttons Row -->
                        <div class="d-flex justify-content-between mt-4">
                            <button form="editForm" type="submit" class="btn btn-outline-purple">Save Changes</button>
                            <form action="../includes/delete_classroom.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this classroom? This action cannot be undone.');">
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
    <script src="../js/error.js"></script>
    <script src="../js/success.js"></script>
</body>
</html>
