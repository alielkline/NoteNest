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

// Check if user already bookmarked the note
$stmt = $pdo->prepare("SELECT * FROM bookmarks WHERE user_id = ? AND note_id = ?");
$stmt->execute([$user_id, $note_id]);
$bookmarked = $stmt->fetch();

if ($bookmarked) {
    // Unbookmark it
    $pdo->prepare("DELETE FROM bookmarks WHERE user_id = ? AND note_id = ?")->execute([$user_id, $note_id]);
    $pdo->prepare("UPDATE classroom_notes SET bookmarkes = bookmarkes - 1 WHERE note_id = ?")->execute([$note_id]);
} else {
    // bookmark it
    $pdo->prepare("INSERT INTO bookmarks (user_id, note_id) VALUES (?, ?)")->execute([$user_id, $note_id]);
    $pdo->prepare("UPDATE classroom_notes SET bookmarkes = bookmarkes + 1 WHERE note_id = ?")->execute([$note_id]);
}

    echo json_encode(['success' => true]);