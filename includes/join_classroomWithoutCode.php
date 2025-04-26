<?php
include '../includes/init.php';
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/classrooms.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$classroom_id = intval($_POST['classroom_id']);

$stmt = $pdo->prepare("INSERT IGNORE INTO classroom_members (user_id, classroom_id) VALUES (?, ?)");
$stmt->execute([$user_id, $classroom_id]);

$stmt = $pdo->prepare("UPDATE classrooms SET members = members + 1 WHERE classroom_id = ?");
$stmt->execute([$classroom_id]);



$_SESSION['success'] = "You have joined the classroom.";
header("Location: ../public/subjects.php?classroom_id=$classroom_id");
exit();
