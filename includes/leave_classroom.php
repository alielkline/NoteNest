<?php
include '../includes/init.php';
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/classrooms.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$classroom_id = intval($_POST['classroom_id']);

// Prevent creator from leaving their own classroom
$stmt = $pdo->prepare("SELECT creator_id FROM classrooms WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);
$creator = $stmt->fetchColumn();

if ($creator == $user_id) {
    $_SESSION['error'] = "Classroom creator can't leave their own classroom.";
    header("Location: ../public/subjects.php?classroom_id=$classroom_id");
    exit();
}

$stmt = $pdo->prepare("DELETE FROM classroom_members WHERE user_id = ? AND classroom_id = ?");
$stmt->execute([$user_id, $classroom_id]);

$stmt = $pdo->prepare("UPDATE classrooms SET members = members - 1 WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);

$_SESSION['success'] = "You have left the classroom.";
header("Location: ../public/classrooms.php");
exit();
