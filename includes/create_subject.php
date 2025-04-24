<?php
include '../includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $classroom_id = intval($_POST['classroom_id']);

    if (empty($name) || empty($classroom_id)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../public/subjects.php?classroom_id=" . urlencode($classroom_id));
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO classroom_subjects (subject_name, classroom_id) VALUES (?, ?)");
    $stmt->execute([$name, $classroom_id]);

    $_SESSION['success'] = "Subject created successfully.";
    header("Location: ../public/subjects.php?classroom_id=" . urlencode($classroom_id));
    exit();
} else {
    $_SESSION['error'] = "An error occurred. Try again later.";
    header("Location: ../public/classrooms.php");
    exit();
}
?>
