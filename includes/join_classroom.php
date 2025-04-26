<?php
include '../includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_code'])) {
    $invite_code = $_POST['invite_code'];

    // Find the classroom using the invite code
    $stmt = $pdo->prepare("SELECT * FROM classrooms WHERE invite_code = ?");
    $stmt->execute([$invite_code]);

    // Check if the classroom exists
    if ($stmt->rowCount() > 0) {
        $classroom = $stmt->fetch();
        $classroom_id = $classroom['classroom_id'];

        // Prevent double join
        $stmt = $pdo->prepare("SELECT * FROM classroom_members WHERE user_id = ? AND classroom_id = ?");
        $stmt->execute([$user_id, $classroom_id]);

        if ($stmt->rowCount() === 0) {
            // Add the user to the classroom
            $stmt = $pdo->prepare("INSERT INTO classroom_members (user_id, classroom_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $classroom_id]);
            $stmt = $pdo->prepare("UPDATE classrooms SET members = members + 1 WHERE classroom_id = ?");
            $stmt->execute([$classroom_id]);
            $_SESSION['success'] = 'Joined classroom successfuly';
            header("Location: ../public/classrooms.php");
            exit();
        }
        else{
            $_SESSION['error'] = 'Already in this classroom';
            header("Location: ../public/classrooms.php");
            exit();
        }
    } else {
        // Handle invalid invite code (optional)
        $_SESSION['error'] = 'Invalid invite code';
        header("Location: ../public/classrooms.php");
        exit();
    }
}

header("Location: ../public/classrooms.php");
exit();
?>
