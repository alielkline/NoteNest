<?php
include '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['classroom_id'], $_POST['classroomName'], $_POST['describtion'])) {
    $_SESSION['error'] = 'Required fields are missing!';
    header("Location: ../public/subjects.php?classroom_id=$classroom_id");
    exit();
}

$classroom_id = intval($_POST['classroom_id']);
$new_name = trim($_POST['classroomName']);
$new_desc = trim($_POST['describtion']);
$new_visibility = trim($_POST['visibility']);

// Fetch existing classroom
$stmt = $pdo->prepare("SELECT name, description FROM classrooms WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);
$classroom = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$classroom) {
    $_SESSION['error'] = "This classroom doesn't exist!";
    header("Location: ../public/dashboard.php");
    exit();
}

$old_name = trim($classroom['name']);
$old_desc = trim($classroom['description']);
$old_visibility = trim($classroom['visibility']);

// Check if there are changes
if ($new_name === $old_name && $new_desc === $old_desc && $new_visibility === $old_visibility) {
    header("Location: ../public/subjects.php?classroom_id=$classroom_id");
    exit();
}

// Perform update if changed
$stmt = $pdo->prepare("UPDATE classrooms SET name = ?, description = ?, visibility = ? WHERE classroom_id = ?");
$stmt->execute([$new_name, $new_desc, $new_visibility, $classroom_id]);

$_SESSION['success'] = 'Updated classroom successfully';
header("Location: ../public/subjects.php?classroom_id=$classroom_id");
exit();
