<?php
include '../includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(!isset($_GET['classroom_id'])){
    header("Location: ../public/classrooms.php");
    exit();
}

$classroom_id = $_GET['classroom_id'];

$stmt = $pdo->prepare("SELECT * FROM classrooms WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);
$classroom = $stmt->fetch();

if(!$classroom){
    header("Location: ../public/classrooms.php");
    exit();
}

$classroom_name = $classroom['name'];
$classroom_desc = $classroom['description'];
$classroom_date = $classroom['created_at'];
$classroom_invCode = $classroom['invite_code'];
$classroom_visibility = $classroom['visibility'];

$stmt = $pdo->prepare("SELECT * FROM classroom_subjects WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);
$subjects = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM classroom_members WHERE user_id = ? AND classroom_id = ?");
$stmt->execute([$user_id, $classroom_id]);
$is_member = $stmt->fetch() ? true : false;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($classroom_name) ?> - Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/subjects.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
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

        <div class="row mb-1">
            <div class = "col-12">
                <p class="text-muted"><a href="../public/classrooms.php" class="classroom-link">Classrooms</a> / <strong><?= htmlspecialchars($classroom_name) ?></p>
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
            <?php if ($is_member): ?>
            <?php if ($classroom['creator_id'] != $user_id): ?>
                <form action="../includes/leave_classroom.php" method="POST" class="ms-2">
                    <input type="hidden" name="classroom_id" value="<?= $classroom_id ?>">
                    <button class="btn btn-outline-danger" type="submit">
                        <i class="bi bi-box-arrow-left me-1"></i> Leave Classroom
                    </button>
                </form>
            <?php endif; ?>
            <?php else: ?>
                <form action="../includes/join_classroomWithoutCode.php" method="POST" class="ms-2">
                    <input type="hidden" name="classroom_id" value="<?= $classroom_id ?>">
                    <button class="btn btn-outline-success" type="submit">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Join Classroom
                    </button>
                </form>
            <?php endif; ?>
        </div>
        <!-- Classroom details in a row -->
        <div class="row mb-4">
            <!-- Description on a separate row -->
            <div class="col-12">
                <p class="desc"><?= htmlspecialchars($classroom_desc) ?></p>
            </div>
        </div>
        

        <!-- Subjects display -->
        <div class="row g-4">
            <?php if (count($subjects) > 0): ?>
                <?php foreach ($subjects as $subject): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($subject['subject_name']) ?></h5>
                                <a href="subject_detail.php?subject_id=<?= urlencode($subject['subject_id']) ?>&classroom_id=<?= urlencode($classroom_id) ?>" class="btn btn-outline-purple">View Details</a>
                                <span class="notes-count">📝 <?= $subject['notes'] ?> notes</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No subjects available for this classroom.</p>
            <?php endif; ?>
        </div>

        <div class="modal fade" id="createSubjectModal" tabindex="-1" aria-labelledby="createSubjectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createSubjectModalLabel">Create a New Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../includes/create_subject.php" method="POST">
                            <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($classroom_id) ?>">
                            <div class="mb-3">
                                <label for="subjectName" class="form-label">Subject Name</label>
                                <input type="text" class="form-control" id="subjectName" name="name" required>
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
                        <form action="../includes/edit_classroom.php" method="POST">
                            <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($classroom_id) ?>">
                            <div class="mb-3">
                                <label for="classroomName" class="form-label">Classroom Name</label>
                                <input type="text" class="form-control" id="classroomName" name="classroomName" value="<?php echo htmlspecialchars($classroom_name); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="inviteCode" class="form-label">Invite Code:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="inviteCode" value="<?php echo htmlspecialchars($classroom_invCode); ?>" readonly>
                                    <button class="btn btn-purple text-white" type="button" onclick="copyInviteCode()">Copy</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="describtion" class="form-label">Description:</label>
                                <input type="text" class="form-control" id="describtion" name="describtion" value="<?php echo htmlspecialchars($classroom_desc); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibility</label>
                                <select class="form-select" id="visibility" name="visibility">
                                    <option value="public" <?php echo ($classroom_visibility === 'public') ? 'selected' : ''; ?>>Public</option>
                                    <option value="private" <?php echo ($classroom_visibility === 'private') ? 'selected' : ''; ?>>Private</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-purple">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/error.js"></script>
    <script src="../js/success.js"></script>
    <script src="../js/copy.js"></script>
</body>
</html>