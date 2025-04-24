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

$stmt = $pdo->prepare("SELECT * FROM classroom_subjects WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);
$subjects = $stmt->fetchAll();
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

    <div class="container my-4">
        <div class="row mb-1">
            <div class = "col-12">
                <p class="text-muted"><a href="../public/classrooms.php" class="classroom-link">Classrooms</a> / <strong><?= htmlspecialchars($classroom_name) ?></p>
            </div>
        </div>

        <h2 class="fw-bold"><?= htmlspecialchars($classroom_name) ?> - Subjects</h2>

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
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($subject['subject_name']) ?></h5>
                                <a href="subject_detail.php?subject_id=<?= urlencode($subject['subject_id']) ?>&classroom_id=<?= urlencode($classroom_id) ?>" class="btn btn-outline-purple">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No subjects available for this classroom.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
