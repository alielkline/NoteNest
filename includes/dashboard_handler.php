<?php
session_start();
require '../includes/db_connection.php'; // Adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $visibility = $_POST['visibility'] ?? 'private';


    // Get creator_id from session
    $creator_id = $_SESSION['user_id'] ?? null; // Make sure user is logged in

    if ($creator_id && $name && $description) {
        $invite_code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);


        $stmt = $pdo->prepare("INSERT INTO classrooms (name, creator_id, invite_code description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $creator_id, $invite_code, $created_at, $description]);

        header("Location: dashboard.php");
        exit();
    } else {
        // Handle missing fields or unauthorized user
        echo "Error: Missing data or not logged in.";
    }
} else {
    header("Location: dashboard.php");
    exit();
}
