<?php
    require_once __DIR__ . '/../../config/init.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    
    require_once __DIR__ . '/../../controllers/ClassroomController.php'; 
    
    $controller = new ClassroomController();
    $data = $controller->getClassrooms();
    $classrooms = $data['classrooms'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Classrooms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/classroom.css">
</head>
<body>
    <?php include '../partials/navbar.php'; ?>
    
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

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 classroom-header">
            <div>
                <h2 class="fw-bold">Classrooms</h2>
                <p class="text-muted">Join or create classrooms to collaborate</p>
            </div>
            <div class="btn-group">
                <!-- Join Classroom Button -->
                <button class="btn btn-outline-purple" data-bs-toggle="modal" data-bs-target="#joinClassroomModal">Join Classroom</button>

                <!-- New Classroom Button -->
                <button class="btn btn-purple text-white" data-bs-toggle="modal" data-bs-target="#createClassroomModal">
                    <i class="bi bi-plus-lg me-1"></i> New Classroom
                </button>
            </div>
        </div>

        <div class="row g-4 card-grid">
            <?php foreach ($classrooms as $classroom): ?>
                <div class="col-md-6 col-lg-4 d-flex">
                    <a href="subjects.php?classroom_id=<?= $classroom['classroom_id'] ?>" class="text-decoration-none text-dark w-100">
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
        </div>

        <!-- Modal for Join Classroom -->
        <div class="modal fade" id="joinClassroomModal" tabindex="-1" aria-labelledby="joinClassroomModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinClassroomModalLabel">Join a Classroom</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../../controllers/ClassroomController.php?action=Codejoin" method="POST">
                            <div class="mb-3">
                                <label for="inviteCode" class="form-label">Enter Invite Code</label>
                                <input type="text" class="form-control" id="inviteCode" name="invite_code" required>
                            </div>
                            <button type="submit" class="btn btn-outline-purple">Join Classroom</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Create Classroom -->
        <div class="modal fade" id="createClassroomModal" tabindex="-1" aria-labelledby="createClassroomModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createClassroomModalLabel">Create a New Classroom</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../../controllers/ClassroomController.php?action=create" method="POST">
                            <div class="mb-3">
                                <label for="classroomName" class="form-label">Classroom Name</label>
                                <input type="text" class="form-control" id="classroomName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="classroomDescription" class="form-label">Classroom Description</label>
                                <textarea class="form-control" id="classroomDescription" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibility</label>
                                <select class="form-select" id="visibility" name="visibility" required>
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-purple">Create Classroom</button>
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
