<?php
session_start();
require_once 'db.inc.php'; // Adjust path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $visibility = $_POST['visibility'] ?? 'private';
    $creator_id = $_SESSION['user_id'] ?? null;

    if ($creator_id && $name && $description) {
        $invite_code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

        $stmt = $pdo->prepare("INSERT INTO classrooms (name, creator_id, invite_code, description, visibility) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $creator_id, $invite_code, $description, $visibility]);

        header("Location: ../public/dashboard.php");
        exit();
    } else {
        echo "Error: Missing data or not logged in.";
    }
} else {
    header("Location: ../public/dashboard.php");
    exit();
}
