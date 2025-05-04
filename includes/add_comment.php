<?php
include 'init.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $note_id = $_POST['note_id'];
    $comment_text = trim($_POST['content']);

    $stmt = $pdo->prepare("INSERT INTO comments (note_id, user_id, comment_text) VALUES (:note_id, :user_id, :comment_text)");

    $stmt->execute([
        ':note_id' => $note_id,
        ':user_id' => $user_id,
        ':comment_text' => $comment_text
    ]);

    $_SESSION['success'] = 'Comment Added Successfuly';
    header("Location: ../public/single_note.php?note_id=" . $note_id);
    exit();
}
