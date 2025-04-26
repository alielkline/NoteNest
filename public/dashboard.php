<?php
include '../includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$username = $user['username'];

// Fetch classrooms (public or those created by the user)
$stmt = $pdo->prepare("SELECT c.*
FROM classrooms c
JOIN classroom_members cm ON c.classroom_id = cm.classroom_id
WHERE cm.user_id = ?;");
$stmt->execute([$user_id]);
$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 1. Get selected filter/sort from request (GET or POST)
$classroom_filter = isset($_GET['classroom_id']) && $_GET['classroom_id'] !== 'all' ? $_GET['classroom_id'] : null;
$sort_order = isset($_GET['sort']) && $_GET['sort'] === 'oldest' ? 'ASC' : 'DESC';

// 2. Base query to get notes
$note_query = "
    SELECT cn.*
    FROM classroom_notes cn
    JOIN classroom_subjects cs ON cn.subject_id = cs.subject_id
";

// 3. Apply filter if a specific classroom is selected
$params = [];
if ($classroom_filter) {
    $note_query .= " WHERE cs.classroom_id = ?";
    $params[] = $classroom_filter;
}

// 4. Add sorting
$note_query .= " ORDER BY cn.upload_date $sort_order";

// 5. Prepare and execute
$note_stmt = $pdo->prepare($note_query);
$note_stmt->execute($params);
$notes = $note_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/classroom.css" rel="stylesheet">
    <link herf="../css/filter_notes.css" rel="stylesheet">
    <title>Dashboard</title>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="main-content p-4 w-100 container">

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <div>
                <h2 class="mb-0">Dashboard</h2>
                <p class="text-muted mb-0">Manage your classrooms and notes</p>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="#" class="btn me-2" data-bs-toggle="modal" data-bs-target="#createClassroomModal"
                    style="background-color: #d9c5f5; color: #5f2eb5; border: none;">
                    <i class="bi bi-plus"></i> New Classroom
                </a>
                <a href="#" class="btn btn-outline-dark">
                    <i class="bi bi-plus"></i> New Note
                </a>
            </div>
        </div>


        <!-- class rooms -->
        <h5 class="mt-4">
            <i class="bi bi-mortarboard-fill text-primary me-2"></i> Your Classrooms
        </h5>

        <div class="container mt-4">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Loop through classrooms -->
                <?php foreach ($classrooms as $classroom): ?>
                    <div class="col-md-6 col-lg-4 d-flex">
                        <a href="../public/subjects.php?classroom_id=<?= $classroom['classroom_id'] ?>" class="text-decoration-none text-dark w-100">
                            <div class="card card-custom p-3 flex-fill position-relative">
                                <h5 class="fw-semibold"><?= htmlspecialchars($classroom['name']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($classroom['description']) ?></p>
                                <div class="d-flex justify-content-between text-muted small mt-auto">
                                    <span>👥 <?= $classroom['members'] ?> members</span>
                                    <?php if ($user_id == $classroom['creator_id']): ?>
                                        <span class="admin-badge">Admin</span>
                                    <?php endif; ?>
                                </div>
                                <span class="stretched-link"></span> <!-- Makes the whole card clickable -->
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>

                <!-- Create New Classroom Card -->
                <div class="col-md-6 col-lg-4 d-flex">
                    <div class="card card-custom p-3 flex-fill text-center position-relative">
                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                            <a href="#" class="btn d-flex align-items-center justify-content-center mb-3"
                                data-bs-toggle="modal" data-bs-target="#createClassroomModal"
                                style="width: 60px; height: 60px; border-radius: 50%; background-color: #d9c5f5; color: #5f2eb5; border: none; z-index: 2;">
                                <i class="bi bi-plus fs-3"></i>
                            </a>
                            <h5 class="fw-semibold mt-2">Create a New Classroom</h5>
                            <p class="text-muted small">Start collaborating with your classmates</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- notes -->

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <div class="text-center my-4">
                <h5 class="d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-book text-primary me-2"></i> My Notes
                </h5>
            </div>
            <?php include "../includes/filter_notes.php"; ?>

            <div class="row g-4" id="notes-container">
                <?php foreach ($notes as $index => $note): ?>
                    <div class="col-12 note-card <?= $index >= 4 ? 'd-none extra-note' : '' ?>">
                        <div class="card card-custom p-3 d-flex flex-column position-relative w-100">
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

            <?php if (count($notes) > 4): ?>
                <div class="text-center mt-3">
                    <button class="btn btn-outline-dark" id="toggle-notes-btn">View More</button>
                </div>
            <?php endif; ?>

            <?php if (empty($notes)): ?>
                <div class="col-12">
                    <div class="alert alert-secondary text-center" role="alert">
                        No notes found.
                    </div>
                </div>
            <?php endif; ?>
        </div>











        <!-- Create Classroom Modal -->
        <div class="modal fade" id="createClassroomModal" tabindex="-1" aria-labelledby="createClassroomLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="../includes/create_classroom.php">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createClassroomLabel">Create a New Classroom</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="classroomName" class="form-label">Classroom Name</label>
                                <input type="text" class="form-control" id="classroomName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="classroomDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="classroomDescription" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="classroomVisibility" class="form-label">Visibility</label>
                                <select class="form-select" id="classroomVisibility" name="visibility" required>
                                    <option value="public">Public</option>
                                    <option value="private" selected>Private</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn" style="background-color: #d9c5f5; color: #5f2eb5; border: none;">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/dashboard.js"></script>
</body>

</html>