<?php
include 'init.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$note_id = $_POST['note_id'] ?? null;

if (!$note_id) {
    echo json_encode(['success' => false, 'error' => 'No note ID']);
    exit;
}

// Check if user already liked the note
$stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND note_id = ?");
$stmt->execute([$user_id, $note_id]);
$liked = $stmt->fetch();

if ($liked) {
    // Unlike it
    $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND note_id = ?")->execute([$user_id, $note_id]);
    $pdo->prepare("UPDATE classroom_notes SET likes = likes - 1 WHERE note_id = ?")->execute([$note_id]);
} else {
    // Like it
    $pdo->prepare("INSERT INTO likes (user_id, note_id) VALUES (?, ?)")->execute([$user_id, $note_id]);
    $pdo->prepare("UPDATE classroom_notes SET likes = likes + 1 WHERE note_id = ?")->execute([$note_id]);
}

// Get updated like count
$stmt = $pdo->prepare("SELECT likes FROM classroom_notes WHERE note_id = ?");
$stmt->execute([$note_id]);
$likes = $stmt->fetchColumn();

echo json_encode(['success' => true, 'likes' => $likes]);