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
$stmt = $pdo->prepare("SELECT * FROM classrooms WHERE creator_id = ? OR visibility = 'public'");
$stmt->execute([$user_id]);
$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <title>Dashboard</title>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <!-- head of dashboard -->
    <div class="main-content p-4 w-100 container">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <div>
                <h2 class="mb-0">Dashboard</h2>
                <p class="text-muted mb-0">Manage your classrooms and notes</p>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="#" class="btn me-2 " data-bs-toggle="modal" data-bs-target="#createClassroomModal" style="background-color: #d9c5f5; color: #5f2eb5; border: none;">
                    <i class="bi bi-plus"></i> New Classroom
                </a>
                <a href="#" class="btn btn-outline-dark">
                    <i class="bi bi-plus"></i> New Note
                </a>
            </div>
        </div>


        <!-- Tabs -->
        <ul class="nav nav-tabs mt-4 custom-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab">Overview</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="notes-tab" data-bs-toggle="tab" href="#notes" role="tab">My Notes</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="bookmarked-tab" data-bs-toggle="tab" href="#bookmarked" role="tab">Bookmarked</a>
            </li>
        </ul>


        <div class="tab-content mt-3">
            <!-- class rooms section -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <h5 class="mt-4">
                    <i class="bi bi-mortarboard-fill text-primary me-2"></i> Your Classrooms
                </h5>
                <!-- class rooms if exist -->
                <div class="container mt-4">
                    <div class="row g-4">
                        <!-- Loop through classrooms -->
                        <?php foreach ($classrooms as $classroom): ?>
                            <div class="col-md-4">
                                <div class="card shadow-sm text-center h-100">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <h5 class="card-title"><?= htmlspecialchars($classroom['name']) ?></h5>
                                        <p class="text-muted mb-0"><?= htmlspecialchars($classroom['description']) ?></p>
                                        <span class="badge bg-<?= $classroom['visibility'] === 'public' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($classroom['visibility']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Create New Classroom Card (always last) -->
                        <div class="col-md-4">
                            <div class="card shadow-sm text-center h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <a href="#" class="btn d-flex align-items-center justify-content-center mb-3"
                                        data-bs-toggle="modal" data-bs-target="#createClassroomModal"
                                        style="width: 60px; height: 60px; border-radius: 50%; background-color: #d9c5f5; color: #5f2eb5; border: none;">
                                        <i class="bi bi-plus fs-3"></i>
                                    </a>
                                    <h5 class="card-title">Create a New Classroom</h5>
                                    <p class="text-muted mb-0">Start collaborating with your classmates</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="notes" role="tabpanel">
                <p>This is My Notes tab content.</p>
            </div>
            <div class="tab-pane fade" id="bookmarked" role="tabpanel">
                <p>This is the Bookmarked tab content.</p>
            </div>
        </div>


    </div>
    </div>


    <!-- Create Classroom Modal -->
    <div class="modal fade" id="createClassroomModal" tabindex="-1" aria-labelledby="createClassroomLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="../includes/dashboard_handler.php">
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
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>