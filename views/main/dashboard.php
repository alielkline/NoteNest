<?php
// app/views/main/dashboard.php
require_once __DIR__ . '/../../controllers/DashboardController.php';

$controller = new DashboardController();
$data = $controller->getDashboardData();
$classrooms = $data['classrooms'];
$notes = $data['notes'];
$user_id = $data['user_id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../../public/assets/css/main.css">
    <link rel="stylesheet" href="../../public/assets/css/notes.css">
    <link rel="stylesheet" href="../../public/assets/css/dashboard.css">
    <link rel="stylesheet" href="../../public/assets/css/classroom.css">
    <link rel="stylesheet" href="../../public/assets/css/filter_notes.css">
    <title>Dashboard</title>
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
    <div class="main-content p-4 w-100 container">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <div>
                <h2 class="mb-0">Dashboard</h2>
                <p class="text-muted mb-0">Manage your classrooms and notes</p>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="#" class="btn new-classroom-btn me-2" data-bs-toggle="modal" data-bs-target="#createClassroomModal">
                    <i class="bi bi-plus"></i> New Classroom
                </a>
            </div>
        </div>

        <!-- classrooms -->
        <h5 class="mt-4">
            <i class="bi bi-mortarboard-fill purple-icon me-2"></i> Your Classrooms
        </h5>

        <div class="container mt-4">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($classrooms as $classroom): ?>
                    <div class="col-md-6 col-lg-4 d-flex">
                        <a href="../pages/subjects.php?classroom_id=<?= $classroom['classroom_id'] ?>" class="text-decoration-none text-dark w-100">
                            <div class="card card-custom p-3 flex-fill position-relative">
                                <h5 class="fw-semibold"><?= htmlspecialchars($classroom['name']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($classroom['description']) ?></p>
                                <div class="d-flex justify-content-between text-muted small mt-auto">
                                    <span>👥 <?= $classroom['members'] ?> members</span>
                                    <?php if ($user_id == $classroom['creator_id']): ?>
                                        <span class="admin-badge">Admin</span>
                                    <?php endif; ?>
                                </div>
                                <span class="stretched-link"></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- notes -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <div class="text-center my-4">
                <h5 class="d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-book-fill purple-icon me-2"></i> My Notes
                </h5>
            </div>
            <form method="GET" class="d-flex flex-wrap justify-content-center align-items-center gap-3">
                <!-- Filter by Classroom -->
                <div>
                    <select name="classroom_id" id="classroomFilter" class="form-select d-inline-block w-auto ">
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
                    <select name="sort" id="sortOrder" class="form-select d-inline-block w-auto">
                        <option value="newest" <?= (!isset($_GET['sort']) || $_GET['sort'] === 'newest') ? 'selected' : '' ?>>Newest</option>
                        <option value="oldest" <?= (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="btn custom-style">
                        <i class="bi bi-filter"></i> Apply
                    </button>
                </div>
            </form>
        </div>

        <!-- Notes List -->
        <?php include '../partials/notes.php'; ?>

        <!-- Create Classroom Modal -->
        <div class="modal fade" id="createClassroomModal" tabindex="-1" aria-labelledby="createClassroomLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="../../controllers/ClassroomController.php?action=create">
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
    <script src="../../public/assets/js/dashboard.js"></script>
    <script src="../../public/assets/js/error.js"></script>
    <script src="../../public/assets/js/success.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.querySelector('form');
            const notesContainer = document.getElementById('notes-container');

            // Delegate click event for dynamic "View More" button
            notesContainer.addEventListener('click', function(event) {
                if (event.target && event.target.id === 'toggle-notes-btn') {
                    const extraNotes = document.querySelectorAll('.extra-note');
                    const isHidden = extraNotes[0]?.classList.contains('d-none');
                    extraNotes.forEach(note => note.classList.toggle('d-none'));
                    event.target.textContent = isHidden ? 'View Less' : 'View More';

                    if (isHidden && extraNotes.length > 0) {
                        extraNotes[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });

            // Handle form submission for sorting/filtering
            filterForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const params = new URLSearchParams(new FormData(filterForm)).toString();

                fetch(window.location.pathname + '?' + params, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(data => {
                        notesContainer.innerHTML = data;
                        notesContainer.scrollIntoView({
                            behavior: 'smooth'
                        });
                        // No need to re-bind the button — event delegation handles it
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>

</body>

</html>