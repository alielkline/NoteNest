<?php
session_start();
require_once 'db.inc.php'; // adjust if using different name

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $visibility = $_POST['visibility'];
    $creator_id = $_SESSION['user_id']; // make sure session has user_id

    // Generate a random invite code
    $invite_code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 8);
    // Insert into DB
    $stmt = $pdo->prepare("INSERT INTO classrooms (name, creator_id, invite_code, description, visibility) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $creator_id, $invite_code, $description, $visibility]);


    header("Location: ../public/dashboard.php");
    exit();
}
